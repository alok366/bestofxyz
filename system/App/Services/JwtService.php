<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Exception;

class JwtService
{
    private string $secret;
    private string $algorithm;
    private int $accessTtl;
    private int $refreshTtl;
    private string $issuer;

    public function __construct()
    {
        $config = config('auth.jwt') ?? [];
        $this->secret = $config['secret'] ?? env('JWT_SECRET', 'secret');
        $this->algorithm = $config['algorithm'] ?? 'HS256';
        $this->accessTtl = (int) ($config['access_ttl'] ?? 900);
        $this->refreshTtl = (int) ($config['refresh_ttl'] ?? 604800);
        $this->issuer = $config['issuer'] ?? 'https://bestofxyz.com';
    }

    /**
     * Generate an access token and refresh token pair.
     *
     * @param int $userId
     * @param string $role
     * @return array
     */
    public function generateTokenPair(int $userId, string $role = 'user'): array
    {
        $now = time();
        $jti = bin2hex(random_bytes(16));

        $accessPayload = [
            'iss'  => $this->issuer,
            'sub'  => $userId,
            'role' => $role,
            'iat'  => $now,
            'exp'  => $now + $this->accessTtl,
        ];

        $refreshPayload = [
            'iss'  => $this->issuer,
            'sub'  => $userId,
            'role' => $role,
            'jti'  => $jti,
            'type' => 'refresh',
            'iat'  => $now,
            'exp'  => $now + $this->refreshTtl,
        ];

        $accessToken = JWT::encode($accessPayload, $this->secret, $this->algorithm);
        $refreshToken = JWT::encode($refreshPayload, $this->secret, $this->algorithm);

        // Store refresh token jti in Redis with TTL if Redis is available
        try {
            RedisService::set("jwt:refresh:{$jti}", [
                'user_id' => $userId,
                'role'    => $role,
                'created' => $now,
            ], $this->refreshTtl);
        } catch (\Throwable $e) {
            // Redis is optional
        }

        return [
            'access_token'  => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type'    => 'Bearer',
            'expires_in'    => $this->accessTtl,
        ];
    }

    /**
     * Validate and decode a JWT token string.
     *
     * @param string $token
     * @return array
     * @throws ExpiredException
     * @throws Exception
     */
    public function validateToken(string $token): array
    {
        $decoded = JWT::decode($token, new Key($this->secret, $this->algorithm));
        return (array) $decoded;
    }

    /**
     * Refresh a token pair given a valid refresh token.
     *
     * @param string $refreshToken
     * @return array
     * @throws ExpiredException
     * @throws Exception
     */
    public function refreshTokenPair(string $refreshToken): array
    {
        $payload = $this->validateToken($refreshToken);

        if (($payload['type'] ?? '') !== 'refresh') :
            throw new Exception('Token is not a refresh token.');
        endif;

        $jti = $payload['jti'] ?? null;
        if ($jti) :
            // If Redis has recorded refresh tokens, check if this one was revoked
            try {
                if (RedisService::exists("jwt:revoked:{$jti}")) :
                    throw new Exception('Refresh token has been revoked.');
                endif;
                $this->revokeRefreshToken($jti);
            } catch (\Throwable $e) {
                // Redis is optional
            }
        endif;

        $userId = (int) ($payload['sub'] ?? 0);
        $role = (string) ($payload['role'] ?? 'user');

        return $this->generateTokenPair($userId, $role);
    }

    /**
     * Revoke a refresh token by JTI.
     *
     * @param string $jti
     * @return void
     */
    public function revokeRefreshToken(string $jti): void
    {
        try {
            RedisService::delete("jwt:refresh:{$jti}");
            RedisService::set("jwt:revoked:{$jti}", 1, $this->refreshTtl);
        } catch (\Throwable $e) {
            // Redis is optional
        }
    }
}
