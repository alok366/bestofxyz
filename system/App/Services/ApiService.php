<?php

namespace App\Services;

use App\Repositories\Smart\SmartCustomerRepository;
use Utils\Cipher;
use App\Services\RedisService;

class ApiService
{
    private SmartCustomerRepository $customerRepo;
    private array $defaultLimits = [
        'requests' => 100,  // Default fallback
        'window' => 3600    // 1 hour window
    ];
    private string $cachePrefix = 'api_rate_limit';

    public function __construct()
    {
        $this->customerRepo = new SmartCustomerRepository();
    }


    public function checkRateLimit(array $resourceDeveloper): array
    {
        $limit = [
            'requests' => $resourceDeveloper['apiRequestsPerHour'] ?? $this->defaultLimits['requests'],
            'window' => $this->defaultLimits['window']
        ];
        if (RedisService::getInstance()) :
            return $this->checkRateLimitRedis($resourceDeveloper['id'], $limit);
        endif;

        return $this->checkRateLimitFile($resourceDeveloper['id'], $limit);
    }

    public function recordRequest(int $developerId): void
    {
        if (RedisService::getInstance()) :
            $this->recordRequestRedis($developerId);
        else :
            $this->recordRequestFile($developerId);
        endif;
    }

    /**
     * Redis-based rate limiting (sliding window)
     */
    private function checkRateLimitRedis(int $developerId, array $limit): array
    {
        $key = RedisService::createCacheKey($this->cachePrefix, 'requests', $developerId);

        $redis = RedisService::getInstance();
        $now = time();
        $window = $limit['window'];
        $maxRequests = $limit['requests'];

        // Remove expired entries (sliding window)
        $windowStart = $now - $window;
        $redis->zRemRangeByScore($key, 0, $windowStart);

        // Count current requests in window
        $current = $redis->zCard($key);

        // Calculate reset time
        $oldestRequest = $redis->zRange($key, 0, 0, ['WITHSCORES' => true]);
        $resetTime = $oldestRequest ? (int)array_values($oldestRequest)[0] + $window : $now + $window;

        return [
            'allowed' => $current < $maxRequests,
            'limit' => $maxRequests,
            'remaining' => max(0, $maxRequests - $current),
            'retry_after' => $current >= $maxRequests ? ($resetTime - $now) : 0,
            'reset_time' => $resetTime,
            'current' => $current
        ];
    }

    private function recordRequestRedis(int $developerId): void
    {
        $key = RedisService::createCacheKey($this->cachePrefix, 'requests', $developerId);
        $redis = RedisService::getInstance();
        $now = time();

        // Add request with timestamp as score
        $redis->zAdd($key, $now, uniqid());

        // Set expiry for cleanup (2x window to be safe)
        $redis->expire($key, 7200);
    }

    /**
     * File-based fallback
     */
    private function checkRateLimitFile(int $developerId, array $limit): array
    {
        $key = "api_rate_limit_{$developerId}";
        $data = $this->getFileCache($key);
        $current = 0;
        $windowStart = time();

        if ($data && $data['expires'] > time()) :
            $current = $data['count'];
            $windowStart = $data['window_start'];
        endif;

        $windowEnd = $windowStart + $limit['window'];

        return [
            'allowed' => $current < $limit['requests'],
            'limit' => $limit['requests'],
            'remaining' => max(0, $limit['requests'] - $current),
            'retry_after' => $current >= $limit['requests'] ? ($windowEnd - time()) : 0,
            'reset_time' => $windowEnd,
            'current' => $current
        ];
    }

    private function recordRequestFile(int $developerId): void
    {
        $key = "api_rate_limit_{$developerId}";
        $data = $this->getFileCache($key);

        if ($data && $data['expires'] > time()) :
            $data['count']++;
        else :
            $data = [
                'count' => 1,
                'window_start' => time(),
                'expires' => time() + $this->defaultLimits['window']
            ];
        endif;

        $this->setFileCache($key, $data);
    }

    public function validateApiKey(string $apiKey): ?array
    {
        $apiKeyDecoded = Cipher::decodeAPI($apiKey);
        $cacheKey = RedisService::createCacheKey('api_key_validation', md5($apiKeyDecoded));
        $cached = RedisService::get($cacheKey);

        if ($cached) :
            return $cached ? (array)$cached : null;
        endif;

        $resource = $this->customerRepo->findByUsernameOrId($apiKeyDecoded);

        if (!$resource) :
            RedisService::set($cacheKey, null, 60); // Cache negative result for 60 seconds
            return null;
        endif;

        $ttl = $resource ? 300 : 60;

        $developer = [
            'id' => $resource->id,
            'username' => $resource->email,
            'apiRequestsPerHour' => $resource->apiRequestsPerHour,
        ];

        RedisService::set($cacheKey, $developer, $ttl);

        return $developer;
    }


    /**
     * @todo
     * Admin function to Reset rate limits for a developer
     * When this called we need to make sure to clear both Redis and file caches
     */
    public function resetRateLimit(int $developerId): void
    {
        if (RedisService::getInstance()) :
            $key = RedisService::createCacheKey($this->cachePrefix, 'requests', $developerId);
            RedisService::delete($key);
        else :
            $key = "api_rate_limit_{$developerId}";
            $this->deleteFileCache($key);
        endif;

        // Also clear the user limits cache if we have user email
        // Note: This would require storing user email with developer ID mapping
    }


    // File cache helper methods (unchanged)
    private function getFileCache(string $key): ?array
    {
        $cacheDir = sys_get_temp_dir() . '/bestofxyz_api_cache';
        $file = $cacheDir . '/' . md5($key) . '.cache';

        if (!file_exists($file)) :
            return null;
        endif;

        $content = file_get_contents($file);
        $data = json_decode($content, true);

        if (!$data || $data['expires'] < time()) :
            unlink($file);
            return null;
        endif;

        return $data;
    }

    private function setFileCache(string $key, array $data): void
    {
        $cacheDir = sys_get_temp_dir() . '/bestofxyz_api_cache';
        if (!is_dir($cacheDir)) :
            mkdir($cacheDir, 0755, true);
        endif;

        $file = $cacheDir . '/' . md5($key) . '.cache';
        file_put_contents($file, json_encode($data), LOCK_EX);
    }

    private function deleteFileCache(string $key): void
    {
        $cacheDir = sys_get_temp_dir() . '/bestofxyz_api_cache';
        $file = $cacheDir . '/' . md5($key) . '.cache';
        if (file_exists($file)) :
            unlink($file);
        endif;
    }
}
