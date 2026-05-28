<?php

namespace Framework\Console\Commands;

use Framework\Console\BaseCommand;
use App\Services\RedisService;

/**
 * Clear session and JWT refresh token data from Redis.
 *
 * Removes all PHP session keys (pksess:*) and JWT refresh token keys
 * (jwt:refresh:*, jwt:user_refresh:*) from Redis. Useful for forcing
 * all users to re-authenticate after a deploy or security incident.
 */
class CacheSessionsCommand extends BaseCommand
{
    /**
     * Get the command signature.
     *
     * @return string
     */
    public function signature(): string
    {
        return 'cache:sessions';
    }

    /**
     * Get the command description.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Clear sessions and JWT refresh tokens from Redis';
    }

    /**
     * Get detailed help text for this command.
     *
     * @return string
     */
    public function help(): string
    {
        return <<<'HELP'
  Usage:
    php artisan cache:sessions [options]

  Options:
    --jwt-only    Clear only JWT refresh tokens (keep user sessions intact)
    --help, -h    Show this help message

  Examples:
    php artisan cache:sessions              Clear all sessions + JWT tokens (forces re-login)
    php artisan cache:sessions --jwt-only   Clear JWT tokens only (browser sessions stay active)

  What gets cleared:
    Sessions       pksess:*             PHP session data (Redis)
    JWT refresh    jwt:refresh:*        Individual refresh tokens
    JWT user map   jwt:user_refresh:*   Per-user token tracking (for bulk revocation)

  When to use:
    - After a deploy that changes session structure
    - After a security incident (force all users to re-authenticate)
    - After rotating JWT_SECRET in .env
    - Use --jwt-only when you want to invalidate API tokens without disrupting browser sessions
HELP;
    }

    /**
     * Clear session and JWT data from Redis.
     *
     * @param array $args CLI arguments. Supports --jwt-only to clear only JWT tokens.
     * @return int Exit code.
     */
    public function handle(array $args): int
    {
        $options = $this->parseOptions($args);
        $jwtOnly = $this->option($options, 'jwt-only', false);

        $redis = RedisService::getInstance();

        if (!$redis) :
            $this->error("Redis is not available. Cannot clear sessions.");
            return 1;
        endif;

        $totalCleared = 0;

        if (!$jwtOnly) :
            $sessionPrefix = config('app.session.prefix', 'pksess:');
            $totalCleared += $this->clearKeysByPattern($redis, $sessionPrefix . '*');
            $this->info("Sessions cleared ({$sessionPrefix}*).");
        endif;

        $jwtRefreshCleared = $this->clearKeysByPattern($redis, 'jwt:refresh:*');
        $jwtUserCleared = $this->clearKeysByPattern($redis, 'jwt:user_refresh:*');
        $totalCleared += $jwtRefreshCleared + $jwtUserCleared;

        $this->info("JWT refresh tokens cleared (jwt:refresh:* = {$jwtRefreshCleared}, jwt:user_refresh:* = {$jwtUserCleared}).");
        $this->success("Total keys cleared: {$totalCleared}");

        return 0;
    }

    /**
     * Delete all Redis keys matching a glob pattern using SCAN.
     *
     * @param \Redis $redis Redis instance.
     * @param string $pattern Glob pattern (e.g. 'pksess:*').
     * @return int Number of keys deleted.
     */
    private function clearKeysByPattern(\Redis $redis, string $pattern): int
    {
        $deleted = 0;
        $iterator = null;

        while (($keys = $redis->scan($iterator, $pattern, 100)) !== false) :
            if (!empty($keys)) :
                $deleted += $redis->del($keys);
            endif;
        endwhile;

        return $deleted;
    }
}
