<?php

namespace Framework\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\JwtService;
use App\Repositories\Smart\SmartCustomerRepository;
use Utils\BMLogger;

/**
 * JWT authentication middleware for API routes.
 *
 * Checks the Authorization: Bearer header for a valid JWT access token.
 * If present and valid, loads the user from the database and sets the
 * session login object so all downstream code (AuthService, controllers)
 * works unchanged. If no Authorization header is present, falls back to
 * session auth for backward compatibility.
 */
class JwtAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request The incoming HTTP request.
     * @param Closure $next The next middleware in the pipeline.
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $authHeader = $request->header('Authorization');

        // No Authorization header — fall back to session auth
        if (!$authHeader) :
            return $this->fallbackToSession($request, $next);
        endif;

        // Must be Bearer scheme
        if (!str_starts_with($authHeader, 'Bearer ')) :
            return $this->problemResponse(401, 'Unauthorized', 'Authorization header must use Bearer scheme.');
        endif;

        $token = substr($authHeader, 7);

        if (empty($token)) :
            return $this->problemResponse(401, 'Unauthorized', 'Bearer token is empty.');
        endif;

        try {
            $jwtService = new JwtService();
            $payload = $jwtService->validateToken($token);
        } catch (\Firebase\JWT\ExpiredException $e) {
            return $this->problemResponse(401, 'Token Expired', 'Access token has expired. Use the refresh endpoint to obtain a new token.');
        } catch (\Exception $e) {
            BMLogger::error($e, 'JwtAuthMiddleware::handle');
            return $this->problemResponse(401, 'Unauthorized', 'Invalid access token.');
        }

        // Load the full user object from DB (same as session login flow)
        $userId = (int) $payload['sub'];
        $customerRepo = new SmartCustomerRepository();
        $user = $customerRepo->findByUsernameOrId($userId);

        if (!$user) :
            return $this->problemResponse(401, 'Unauthorized', 'User account not found or inactive.');
        endif;

        // Set session login object so AuthService and controllers work unchanged
        $_SESSION['login'] = $user;

        // Store JWT flag so controllers can distinguish auth method if needed
        $request->attributes->set('auth_via', 'jwt');
        $request->attributes->set('jwt_payload', $payload);

        return $next($request);
    }

    /**
     * Fall back to existing session-based authentication.
     *
     * If no session exists, returns 401. This preserves backward compatibility
     * for browser-based requests that still use cookies.
     *
     * @param Request $request The incoming HTTP request.
     * @param Closure $next The next middleware in the pipeline.
     * @return mixed
     */
    private function fallbackToSession(Request $request, Closure $next)
    {
        if (!isset($_SESSION['login'])) :
            return $this->problemResponse(401, 'Unauthorized', 'Authentication required. Provide a Bearer token or valid session.');
        endif;

        $request->attributes->set('auth_via', 'session');

        return $next($request);
    }

    /**
     * Build an RFC 9457 problem+json response.
     *
     * @param int $status HTTP status code.
     * @param string $title Short error title.
     * @param string $detail Human-readable error detail.
     * @return Response
     */
    private function problemResponse(int $status, string $title, string $detail): Response
    {
        return new Response(
            json_encode([
                'type' => "https://httpstatuses.com/{$status}",
                'title' => $title,
                'detail' => $detail,
                'status' => $status,
            ]),
            $status,
            ['Content-Type' => 'application/problem+json']
        );
    }
}
