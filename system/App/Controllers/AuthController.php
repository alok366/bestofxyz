<?php

namespace App\Controllers;

use App\Services\JwtService;
use Illuminate\Http\Response;

class AuthController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }


    public function showLogin()
    {
        // Prevent caching of login page
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        $isSumoling = !empty($_GET['appsumo']) ? true : false;
        $isWrongCredentials = false;

        if (isset($_SESSION['wpass'])) :
            $isWrongCredentials = true;
            unset($_SESSION['wpass']);
        endif;

        return $this->twig->render('Login/regular.twig', [
            'isSumoling' => $isSumoling,
            'isWrongCredentials' => $isWrongCredentials,
        ]);
    }

    // ── JWT Token Endpoints ────────────────────────────────────────────

    /**
     * Issue a JWT token pair in exchange for username + password.
     *
     * @return Response
     */
    public function issueToken(): Response
    {
        $requestData = $this->request()->all();
        $username = $requestData['username'] ?? '';
        $password = $requestData['password'] ?? '';

        if (empty($username) || empty($password)) :
            return $this->problemResponse(422, 'Validation Error', 'Username and password are required.');
        endif;

        $customerRepo = new SmartCustomerRepository();
        $resource = $customerRepo->findByUsernameOrId($username);

        if (!$resource || !password_verify($password, $resource->password)) :
            return $this->problemResponse(401, 'Unauthorized', 'Invalid username or password.');
        endif;

        try {
            $jwtService = new JwtService();
            $tokenPair = $jwtService->generateTokenPair((int) $resource->id, $resource->userType);
        } catch (\Exception $e) {
            BMLogger::error($e, 'AuthController::issueToken');
            return $this->problemResponse(500, 'Server Error', 'Unable to generate authentication tokens.');
        }

        return new Response(
            json_encode($tokenPair),
            200,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * Refresh an expired access token using a valid refresh token.
     *
     * @return Response
     */
    public function refreshToken(): Response
    {
        $requestData = $this->request()->all();
        $refreshToken = $requestData['refresh_token'] ?? '';

        if (empty($refreshToken)) :
            return $this->problemResponse(422, 'Validation Error', 'Refresh token is required.');
        endif;

        try {
            $jwtService = new JwtService();
            $tokenPair = $jwtService->refreshTokenPair($refreshToken);
        } catch (\Firebase\JWT\ExpiredException $e) {
            return $this->problemResponse(401, 'Token Expired', 'Refresh token has expired. Please log in again.');
        } catch (\Exception $e) {
            BMLogger::error($e, 'AuthController::refreshToken');
            return $this->problemResponse(401, 'Unauthorized', 'Invalid or revoked refresh token.');
        }

        return new Response(
            json_encode($tokenPair),
            200,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * Revoke a refresh token (JWT logout).
     *
     * @return Response
     */
    public function revokeToken(): Response
    {
        $requestData = $this->request()->all();
        $refreshToken = $requestData['refresh_token'] ?? '';

        if (empty($refreshToken)) :
            return $this->problemResponse(422, 'Validation Error', 'Refresh token is required.');
        endif;

        try {
            $jwtService = new JwtService();
            $payload = $jwtService->validateToken($refreshToken);
            $jwtService->revokeRefreshToken($payload['jti']);
        } catch (\Exception $e) {
            // Silently succeed — if the token is already invalid/revoked,
            // the logout intent is still fulfilled
        }

        return new Response(
            json_encode(['message' => 'Token revoked successfully.']),
            200,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * Build an RFC 9457 problem+json error response.
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
