<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Service for managing login and authentication rate limits.
 *
 * Implements a sliding-window rate limiter with primary Redis storage
 * and an automatic file-based fallback when Redis is disabled or unavailable.
 */
class LoginRateLimitService
{
    private array $defaultLimits = [
        'requests' => 5,    // 5 attempts
        'window'   => 120,  // 2 minute window (in seconds)
    ];

    private string $cachePrefix = 'login_rate_limit';
    private string $cacheDir;

    /**
     * Create a new LoginRateLimitService instance.
     *
     * @param array $defaultLimits Optional default limit overrides ['requests' => int, 'window' => int].
     * @param string|null $cacheDir Optional custom directory for file-based fallback.
     */
    public function __construct(array $defaultLimits = [], ?string $cacheDir = null)
    {
        if (!empty($defaultLimits)) :
            $this->defaultLimits = array_merge($this->defaultLimits, $defaultLimits);
        endif;

        $this->cacheDir = $cacheDir ?? (sys_get_temp_dir() . '/bestofxyz_login_throttle_cache');
    }

    /**
     * Check if the given identifier is within rate limits.
     *
     * @param string $identifier Unique client identifier (IP address, username, email, etc.).
     * @param array $customLimits Optional limit overrides ['requests' => int, 'window' => int].
     * @return array{
     *     allowed: bool,
     *     limit: int,
     *     remaining: int,
     *     retry_after: int,
     *     reset_time: int,
     *     current: int
     * }
     */
    public function checkRateLimit(string $identifier, array $customLimits = []): array
    {
        $limit = array_merge($this->defaultLimits, $customLimits);
        $limit['requests'] = max(1, (int) ($limit['requests'] ?? $this->defaultLimits['requests']));
        $limit['window']   = max(1, (int) ($limit['window'] ?? $this->defaultLimits['window']));

        if (RedisService::getInstance()) :
            try {
                return $this->checkRateLimitRedis($identifier, $limit);
            } catch (\Throwable $e) {
                // Graceful fallback to file cache on Redis failure
            }
        endif;

        return $this->checkRateLimitFile($identifier, $limit);
    }

    /**
     * Record a request / login attempt for the given identifier.
     *
     * @param string $identifier Unique client identifier.
     * @param int|null $window Time window in seconds for TTL calculation.
     * @return void
     */
    public function recordRequest(string $identifier, ?int $window = null): void
    {
        $window = max(1, (int) ($window ?? $this->defaultLimits['window']));

        if (RedisService::getInstance()) :
            try {
                $this->recordRequestRedis($identifier, $window);
                return;
            } catch (\Throwable $e) {
                // Graceful fallback to file cache on Redis failure
            }
        endif;

        $this->recordRequestFile($identifier, $window);
    }

    /**
     * Reset / clear rate limit attempts for the given identifier.
     *
     * @param string $identifier Unique client identifier.
     * @return void
     */
    public function resetRateLimit(string $identifier): void
    {
        if (RedisService::getInstance()) :
            try {
                $key = RedisService::createCacheKey($this->cachePrefix, 'attempts', md5($identifier));
                RedisService::delete($key);
            } catch (\Throwable $e) {
                // Ignore Redis errors
            }
        endif;

        $this->deleteFileCache($identifier);
    }

    /**
     * Alias for resetRateLimit.
     *
     * @param string $identifier
     * @return void
     */
    public function clear(string $identifier): void
    {
        $this->resetRateLimit($identifier);
    }

    /**
     * Determine if the identifier has exceeded max attempts.
     *
     * @param string $identifier
     * @param int $maxAttempts
     * @param int $window
     * @return bool
     */
    public function tooManyAttempts(string $identifier, int $maxAttempts = 5, int $window = 120): bool
    {
        $check = $this->checkRateLimit($identifier, [
            'requests' => $maxAttempts,
            'window'   => $window,
        ]);

        return !$check['allowed'];
    }

    /**
     * Get the number of attempts made by the identifier in the current window.
     *
     * @param string $identifier
     * @param int $window
     * @return int
     */
    public function attempts(string $identifier, int $window = 120): int
    {
        $check = $this->checkRateLimit($identifier, [
            'requests' => PHP_INT_MAX,
            'window'   => $window,
        ]);

        return $check['current'];
    }

    /**
     * Get the number of remaining retries before hitting the limit.
     *
     * @param string $identifier
     * @param int $maxAttempts
     * @param int $window
     * @return int
     */
    public function retriesLeft(string $identifier, int $maxAttempts = 5, int $window = 120): int
    {
        $check = $this->checkRateLimit($identifier, [
            'requests' => $maxAttempts,
            'window'   => $window,
        ]);

        return $check['remaining'];
    }

    /**
     * Get the number of seconds until the rate limit is reset for the identifier.
     *
     * @param string $identifier
     * @param int $window
     * @return int
     */
    public function availableIn(string $identifier, int $window = 120): int
    {
        $check = $this->checkRateLimit($identifier, [
            'requests' => 0,
            'window'   => $window,
        ]);

        return $check['retry_after'];
    }

    // ─────────────────────────────────────────────────────────────────
    // Redis Implementation (Sliding Window via Sorted Set)
    // ─────────────────────────────────────────────────────────────────

    private function checkRateLimitRedis(string $identifier, array $limit): array
    {
        $key = RedisService::createCacheKey($this->cachePrefix, 'attempts', md5($identifier));
        $redis = RedisService::getInstance();

        $now = time();
        $window = $limit['window'];
        $maxRequests = $limit['requests'];

        // Remove expired attempts outside the sliding window
        $windowStart = $now - $window;
        $redis->zRemRangeByScore($key, '-inf', (string) $windowStart);

        // Count current attempts in window
        $current = (int) $redis->zCard($key);

        // Calculate reset time based on the oldest attempt in current window
        $oldestRequest = $redis->zRange($key, 0, 0, ['WITHSCORES' => true]);
        $resetTime = $oldestRequest ? ((int) array_values($oldestRequest)[0] + $window) : ($now + $window);
        $retryAfter = ($current >= $maxRequests) ? max(1, $resetTime - $now) : 0;

        return [
            'allowed'     => $current < $maxRequests,
            'limit'       => $maxRequests,
            'remaining'   => max(0, $maxRequests - $current),
            'retry_after' => $retryAfter,
            'reset_time'  => $resetTime,
            'current'     => $current,
        ];
    }

    private function recordRequestRedis(string $identifier, int $window): void
    {
        $key = RedisService::createCacheKey($this->cachePrefix, 'attempts', md5($identifier));
        $redis = RedisService::getInstance();
        $now = time();

        // Add attempt with current timestamp as score
        $redis->zAdd($key, $now, uniqid('', true));

        // Set key TTL to 2x window (minimum 7200s) for cleanup
        $redis->expire($key, max($window * 2, 7200));
    }

    // ─────────────────────────────────────────────────────────────────
    // File-based Implementation (Sliding Window Fallback)
    // ─────────────────────────────────────────────────────────────────

    private function checkRateLimitFile(string $identifier, array $limit): array
    {
        $data = $this->getFileCache($identifier);
        $now = time();
        $window = $limit['window'];
        $maxRequests = $limit['requests'];
        $windowStart = $now - $window;

        // Filter out expired timestamps
        $timestamps = [];
        if ($data && isset($data['timestamps']) && is_array($data['timestamps'])) :
            $timestamps = array_values(array_filter($data['timestamps'], function ($timestamp) use ($windowStart) {
                return (int) $timestamp > $windowStart;
            }));
        endif;

        $current = count($timestamps);
        $oldest = !empty($timestamps) ? min($timestamps) : $now;
        $resetTime = (int) $oldest + $window;
        $retryAfter = ($current >= $maxRequests) ? max(1, $resetTime - $now) : 0;

        return [
            'allowed'     => $current < $maxRequests,
            'limit'       => $maxRequests,
            'remaining'   => max(0, $maxRequests - $current),
            'retry_after' => $retryAfter,
            'reset_time'  => $resetTime,
            'current'     => $current,
        ];
    }

    private function recordRequestFile(string $identifier, int $window): void
    {
        $data = $this->getFileCache($identifier);
        $now = time();
        $windowStart = $now - $window;

        $timestamps = [];
        if ($data && isset($data['timestamps']) && is_array($data['timestamps'])) :
            $timestamps = array_values(array_filter($data['timestamps'], function ($timestamp) use ($windowStart) {
                return (int) $timestamp > $windowStart;
            }));
        endif;

        $timestamps[] = $now;

        $this->setFileCache($identifier, [
            'timestamps' => $timestamps,
            'expires'    => $now + ($window * 2),
        ]);
    }

    private function getFileCache(string $identifier): ?array
    {
        $file = $this->getCacheFilePath($identifier);

        if (!file_exists($file)) :
            return null;
        endif;

        $content = @file_get_contents($file);
        if ($content === false) :
            return null;
        endif;

        $data = json_decode($content, true);
        if (!is_array($data) || (isset($data['expires']) && (int) $data['expires'] < time())) :
            @unlink($file);
            return null;
        endif;

        return $data;
    }

    private function setFileCache(string $identifier, array $data): void
    {
        if (!is_dir($this->cacheDir)) :
            @mkdir($this->cacheDir, 0755, true);
        endif;

        $file = $this->getCacheFilePath($identifier);
        @file_put_contents($file, json_encode($data), LOCK_EX);
    }

    private function deleteFileCache(string $identifier): void
    {
        $file = $this->getCacheFilePath($identifier);
        if (file_exists($file)) :
            @unlink($file);
        endif;
    }

    private function getCacheFilePath(string $identifier): string
    {
        return $this->cacheDir . '/' . md5($identifier) . '.cache';
    }
}
