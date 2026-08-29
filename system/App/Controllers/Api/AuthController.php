<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Services\UserService;
use App\Services\JwtService;
use App\Transformers\UserTransformer;
use App\Models\User;
use Illuminate\Http\Response;

class AuthController extends BaseController
{
    protected UserService $userService;
    protected JwtService $jwtService;

    public function __construct(
        ?UserService $userService = null,
        ?JwtService $jwtService = null
    ) {
        parent::__construct();
        $this->userService = $userService ?? new UserService();
        $this->jwtService = $jwtService ?? new JwtService();
    }

    /**
     * POST /api/auth/register
     *
     * Register a new user account and issue JWT token pair.
     */
    public function register(): Response
    {
        $data = $this->request()->all();

        $rules = [
            'username' => 'required|min:3|max:30|regex:/^[a-zA-Z0-9_]+$/',
            'email'    => 'required|email|max:255',
            'password' => 'required|min:8|max:128',
        ];

        $validator = $this->validator->make($data, $rules);
        if ($validator->fails()) :
            return $this->response->problem(
                422,
                'Validation Error',
                'Registration validation failed.',
                ['errors' => $validator->errors()->toArray()]
            );
        endif;

        $username = trim($data['username']);
        $email = strtolower(trim($data['email']));
        $password = $data['password'];

        // Check uniqueness
        if (User::where('username', $username)->exists()) :
            return $this->response->problem(
                422,
                'Validation Error',
                'Username is already taken.',
                ['errors' => ['username' => ['This username is already registered.']]]
            );
        endif;

        if (User::where('email', $email)->exists()) :
            return $this->response->problem(
                422,
                'Validation Error',
                'Email is already registered.',
                ['errors' => ['email' => ['This email address is already in use.']]]
            );
        endif;

        try {
            $user = $this->userService->register($username, $email, $password);
            $tokenPair = $this->jwtService->generateTokenPair((int) $user->id, $user->role ?? 'user');

            return $this->response->ok(array_merge($tokenPair, [
                'user' => UserTransformer::toProfile($user),
            ]), 201);
        } catch (\Throwable $e) {
            return $this->response->problem(500, 'Server Error',
                config('app.env') === 'production' ? 'An unexpected error occurred.' : 'Failed to register account: ' . $e->getMessage()
            );
        }
    }

    /**
     * POST /api/auth/login
     *
     * Authenticate user credentials and issue JWT token pair.
     */
    public function login(): Response
    {
        $data = $this->request()->all();
        $email = strtolower(trim($data['email'] ?? ''));
        $password = $data['password'] ?? '';

        if (empty($email) || empty($password)) :
            return $this->response->problem(422, 'Validation Error', 'Email and password are required.');
        endif;

        $user = $this->userService->authenticate($email, $password);
        if (!$user) :
            return $this->response->problem(401, 'Unauthorized', 'Invalid email or password.');
        endif;

        try {
            $tokenPair = $this->jwtService->generateTokenPair((int) $user->id, $user->role ?? 'user');
            return $this->response->ok(array_merge($tokenPair, [
                'user' => UserTransformer::toProfile($user),
            ]));
        } catch (\Throwable $e) {
            return $this->response->problem(500, 'Server Error',
                config('app.env') === 'production' ? 'An unexpected error occurred.' : 'Failed to generate auth tokens: ' . $e->getMessage()
            );
        }
    }

    /**
     * POST /api/auth/refresh
     *
     * Refresh access token using a valid refresh token.
     */
    public function refresh(): Response
    {
        $data = $this->request()->all();
        $refreshToken = $data['refresh_token'] ?? '';

        if (empty($refreshToken)) :
            return $this->response->problem(422, 'Validation Error', 'Refresh token is required.');
        endif;

        try {
            $tokenPair = $this->jwtService->refreshTokenPair($refreshToken);
            return $this->response->ok($tokenPair);
        } catch (\Firebase\JWT\ExpiredException $e) {
            return $this->response->problem(401, 'Token Expired', 'Refresh token has expired. Please log in again.');
        } catch (\Throwable $e) {
            return $this->response->problem(401, 'Unauthorized',
                config('app.env') === 'production' ? 'Invalid or revoked refresh token.' : 'Invalid or revoked refresh token: ' . $e->getMessage()
            );
        }
    }

    /**
     * POST /api/auth/logout
     *
     * Revoke refresh token.
     */
    public function logout(): Response
    {
        $data = $this->request()->all();
        $refreshToken = $data['refresh_token'] ?? '';

        if (!empty($refreshToken)) :
            try {
                $payload = $this->jwtService->validateToken($refreshToken);
                if (!empty($payload['jti'])) :
                    $this->jwtService->revokeRefreshToken($payload['jti']);
                endif;
            } catch (\Throwable $e) {
                // Silently succeed
            }
        endif;

        return $this->response->ok(['message' => 'Logged out successfully.']);
    }

    /**
     * GET /api/auth/me
     *
     * Return the authenticated user's profile.
     * Requires auth.jwt middleware — the middleware resolves the user
     * and stores it in the request's auth_user attribute.
     */
    public function me(): Response
    {
        $user = $this->getAuthenticatedUser();
        if (!$user) :
            return $this->response->problem(401, 'Unauthorized', 'Invalid or expired token.');
        endif;

        return $this->response->ok(UserTransformer::toProfile($user));
    }
}
