<?php

namespace Framework\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\LoginRateLimitService;

/**
 * Throttle login attempts to prevent brute-force attacks.
 *
 * Follows the same pattern as OptinSignupMiddleware. Uses Redis
 * sliding-window with file-based fallback via LoginRateLimitService.
 *
 * Usage in routes: ->middleware('auth.throttle:5,2')
 *   5 = max attempts, 2 = decay window in minutes
 */
class LoginThrottleMiddleware
{
    private LoginRateLimitService $loginRateLimitService;
    private int $maxAttempts;
    private int $decayMinutes;

    /**
     * Create a new LoginThrottleMiddleware instance.
     *
     * @param int $maxAttempts Maximum login attempts allowed in the time window.
     * @param int $decayMinutes Time window in minutes.
     */
    public function __construct(int $maxAttempts = 5, int $decayMinutes = 2)
    {
        $this->loginRateLimitService = new LoginRateLimitService();
        $this->maxAttempts = $maxAttempts;
        $this->decayMinutes = $decayMinutes;
    }

    /**
     * Handle an incoming request with rate limiting.
     *
     * @param Request $request The incoming request.
     * @param Closure $next The next middleware handler.
     * @param int|null $maxAttempts Override max attempts via middleware parameter.
     * @param int|null $decayMinutes Override decay window via middleware parameter.
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $maxAttempts = null, $decayMinutes = null)
    {
        // Allow override via middleware parameters
        $maxAttempts = $maxAttempts ?? $this->maxAttempts;
        $decayMinutes = $decayMinutes ?? $this->decayMinutes;

        $ipAddress = $request->ip() ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        // Check rate limit
        $rateLimitCheck = $this->loginRateLimitService->checkRateLimit($ipAddress, [
            'requests' => $maxAttempts,
            'window' => $decayMinutes * 60
        ]);

        if (!$rateLimitCheck['allowed']) :
            return new Response(
                json_encode([
                    'error' => 'Too Many Requests',
                    'message' => "Too many login attempts. Please try again in {$decayMinutes} minute(s).",
                    'retry_after' => $rateLimitCheck['retry_after']
                ]),
                429,
                [
                    'Content-Type' => 'application/json',
                    'Retry-After' => $rateLimitCheck['retry_after'],
                    'X-RateLimit-Limit' => $rateLimitCheck['limit'],
                    'X-RateLimit-Remaining' => '0',
                    'X-RateLimit-Reset' => $rateLimitCheck['reset_time']
                ]
            );
        endif;

        // Record the attempt
        $this->loginRateLimitService->recordRequest($ipAddress);

        $response = $next($request);

        // Add rate limit headers to successful responses
        if (method_exists($response, 'header')) :
            $response
                ->header('X-RateLimit-Limit', $rateLimitCheck['limit'])
                ->header('X-RateLimit-Remaining', $rateLimitCheck['remaining'])
                ->header('X-RateLimit-Reset', $rateLimitCheck['reset_time']);
        endif;

        return $response;
    }
}
