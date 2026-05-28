<?php

namespace Framework\Services;

use App\Services\RedisService;

/**
 * Redis-backed session handler.
 *
 * Implements PHP's SessionHandlerInterface to store session data
 * in Redis instead of the filesystem. Falls back gracefully to
 * PHP native sessions if Redis is unavailable at registration time.
 *
 * Session keys are prefixed with a configurable string to avoid
 * collisions with other Redis data.
 */
class RedisSessionHandler implements \SessionHandlerInterface
{
    private \Redis $redis;
    private int $lifetime;
    private string $prefix;

    /**
     * Create a new RedisSessionHandler.
     *
     * @param \Redis $redis Active Redis connection.
     * @param int $lifetime Session TTL in seconds (default 7200 = 2 hours).
     * @param string $prefix Redis key prefix for session data.
     */
    public function __construct(\Redis $redis, int $lifetime = 7200, string $prefix = 'pksess:')
    {
        $this->redis = $redis;
        $this->lifetime = $lifetime;
        $this->prefix = $prefix;
    }

    /**
     * Open the session store.
     *
     * @param string $savePath The session save path (unused for Redis).
     * @param string $sessionName The session name.
     * @return bool
     */
    public function open(string $savePath, string $sessionName): bool
    {
        return true;
    }

    /**
     * Close the session store.
     *
     * @return bool
     */
    public function close(): bool
    {
        return true;
    }

    /**
     * Read session data from Redis.
     *
     * @param string $sessionId The session ID.
     * @return string|false Serialized session data, or empty string if not found.
     */
    public function read(string $sessionId): string|false
    {
        $data = $this->redis->get($this->prefix . $sessionId);

        return $data !== false ? $data : '';
    }

    /**
     * Write session data to Redis with TTL.
     *
     * @param string $sessionId The session ID.
     * @param string $data Serialized session data.
     * @return bool
     */
    public function write(string $sessionId, string $data): bool
    {
        return $this->redis->setex(
            $this->prefix . $sessionId,
            $this->lifetime,
            $data
        );
    }

    /**
     * Destroy a session in Redis.
     *
     * @param string $sessionId The session ID.
     * @return bool
     */
    public function destroy(string $sessionId): bool
    {
        $this->redis->del($this->prefix . $sessionId);

        return true;
    }

    /**
     * Garbage collection — handled by Redis TTL, no-op here.
     *
     * @param int $maxLifetime Maximum session lifetime.
     * @return int|false Number of deleted sessions (always 0, Redis handles expiry).
     */
    public function gc(int $maxLifetime): int|false
    {
        // Redis handles expiry via TTL — no manual GC needed
        return 0;
    }

    /**
     * Attempt to create and register a RedisSessionHandler.
     *
     * Returns true if Redis is available and handler was registered.
     * Returns false if Redis is unavailable — caller should fall back
     * to PHP native sessions.
     *
     * @param int $lifetime Session TTL in seconds.
     * @param string $prefix Redis key prefix.
     * @return bool Whether Redis session handler was registered.
     */
    public static function register(int $lifetime = 7200, string $prefix = 'pksess:'): bool
    {
        // Respect explicit 'native' driver config — skip Redis entirely
        if (config('app.session.driver', 'redis') === 'native') :
            return false;
        endif;

        $redis = RedisService::getInstance();

        if (!$redis) :
            return false;
        endif;

        $handler = new self($redis, $lifetime, $prefix);
        session_set_save_handler($handler, true);

        return true;
    }
}
