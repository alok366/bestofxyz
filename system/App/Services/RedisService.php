<?php

namespace App\Services;

class RedisService
{
    private static ?\Redis $redis = null;

    /**
     * Check if Redis is enabled
     */
    private static function isRedisEnabled(): bool
    {
        return (bool) config('cache.redis.enabled', false);
    }

    /**
     * Get the Redis instance (singleton)
     */
    public static function getInstance(): ?\Redis
    {
        if (!self::isRedisEnabled()) :
            return null; // Return null if Redis is disabled
        endif;

        if (self::$redis === null) :
            self::$redis = new \Redis();
            try {
                self::$redis->connect(config('cache.redis.host'), config('cache.redis.port'));
                if (config('cache.redis.password') !== null) :
                    self::$redis->auth(config('cache.redis.password'));
                endif;
                self::$redis->ping(); // Test the connection
            } catch (\Exception $e) {
                die("Redis connection failed: " . $e->getMessage());
            }
        endif;

        return self::$redis;
    }

    /**
     * Set a value in Redis with an optional TTL
     */
    public static function set(string $key, $value, int $ttl = 3600): void
    {
        if (!self::isRedisEnabled()) :
            return; // Do nothing if Redis is disabled
        endif;

        $redis = self::getInstance();
        if ($redis) :
            $redis->setex($key, $ttl, is_array($value) ? json_encode($value) : $value);
        endif;
    }

    /**
     * Get a value from Redis
     */
    public static function get(string $key)
    {
        if (!self::isRedisEnabled()) :
            return null; // Return null if Redis is disabled
        endif;

        $redis = self::getInstance();
        if ($redis) :
            $value = $redis->get($key);
            return json_decode($value, true) ?? $value;
        endif;

        return null;
    }

    /**
     * Delete a key from Redis
     */
    public static function delete(string $key): void
    {
        if (!self::isRedisEnabled()) :
            return; // Do nothing if Redis is disabled
        endif;

        $redis = self::getInstance();
        if ($redis) :
            $redis->del($key);
        endif;
    }

    /**
     * Check if a key exists in Redis
     */
    public static function exists(string $key): bool
    {
        if (!self::isRedisEnabled()) :
            return false; // Return false if Redis is disabled
        endif;

        $redis = self::getInstance();
        return $redis ? $redis->exists($key) > 0 : false;
    }

    /**
     * Create a cache key
     */
    public static function createCacheKey(...$parts): string
    {
        $prefix = config('cache.redis.prefix', '');

        // Sanitize parts and ensure they are strings, lowercase, and trimmed
        $formatted = array_map(function ($part) {
            return strtolower(trim((string) $part));
        }, $parts);

        //return $prefix . ':' . implode(':', $formatted);
        return implode(':', $formatted);
    }
}
