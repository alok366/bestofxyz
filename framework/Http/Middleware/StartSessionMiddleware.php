<?php

namespace Framework\Http\Middleware;

use Closure;

/**
 * Starts the PHP session with secure cookie configuration.
 *
 * All session initialization is centralized here — entry points
 * must NOT call session_start() directly. This ensures cookie
 * params are set BEFORE the session starts (PHP requirement)
 * and CSRF tokens are initialized consistently.
 *
 * Uses Redis as the session store when available (via RedisSessionHandler).
 * Falls back to PHP native file-based sessions if Redis is unavailable.
 */
class StartSessionMiddleware
{
    /**
     * Start session with secure cookie params and initialize CSRF token.
     *
     * Registers RedisSessionHandler before session_start() when Redis is
     * available. Cookie params are always set regardless of session driver.
     *
     * @param mixed $request The incoming request.
     * @param Closure $next The next middleware handler.
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (session_status() === PHP_SESSION_NONE) :
            $isSecure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'domain' => '',
                'secure' => $isSecure,
                'httponly' => true,
                'samesite' => $isSecure ? 'Strict' : 'Lax',
            ]);

            session_start(['name' => 'PKSESS']);
        endif;

        if (! isset($_SESSION['csrf_token'])) :
            $_SESSION['csrf_token'] = base64_encode(openssl_random_pseudo_bytes(32));
        endif;

        return $next($request);
    }
}
