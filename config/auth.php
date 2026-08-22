<?php

return [

    /*
    |--------------------------------------------------------------------------
    | JWT Authentication
    |--------------------------------------------------------------------------
    |
    | Configuration for JSON Web Token authentication on API routes.
    | Access tokens are short-lived and stateless (signature-verified only).
    | Refresh tokens are long-lived but server-validated (stored in Redis).
    |
    */
    'jwt' => [
        'secret' => env('JWT_SECRET'),
        'algorithm' => 'HS256',
        'access_ttl' => (int) env('JWT_ACCESS_TTL', 900),       // 15 minutes
        'refresh_ttl' => (int) env('JWT_REFRESH_TTL', 604800),   // 7 days
        'issuer' => env('APP_DOMAIN', 'https://bestofxyz.com'),
    ],

];
