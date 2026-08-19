<?php

namespace Framework\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\JwtService;
use App\Repositories\Smart\SmartUserRepository;

/**
 * JWT authentication middleware for API routes.
 *
 * Checks the Authorization: Bearer header for a valid JWT access token.
 * If present and valid, loads the user from the database and sets the
 * session login object and request attribute.
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
        } catch (\Throwable $e) {
            return $this->problemResponse(401, 'Unauthorized', 'Invalid access token.');
        }

        $userId = (int) ($payload['sub'] ?? 0);
        $userRepo = new SmartUserRepository();
        $user = $userRepo->find($userId);

        if (!$user) :
            return $this->problemResponse(401, 'Unauthorized', 'User account not found or inactive.');
        endif;

        // Set session login object so AuthService and controllers work unchanged
        $_SESSION['login'] = $user;

        // Store user in request attributes
        $request->attributes->set('auth_user', $user);
        $request->attributes->set('auth_via', 'jwt');
        $request->attributes->set('jwt_payload', $payload);

        return $next($request);
    }

    /**
     * Fall back to existing session-based authentication.
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

        $request->attributes->set('auth_user', $_SESSION['login']);
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
