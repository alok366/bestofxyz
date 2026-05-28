<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | Determines the environment the application is running in.
    | Values: 'development', 'staging', 'production'
    |
    */
    'env' => env('MIX_APP_ENV', 'development'),

    /*
    |--------------------------------------------------------------------------
    | Application Title
    |--------------------------------------------------------------------------
    */
    'title' => 'bestofxyz',

    /*
    |--------------------------------------------------------------------------
    | Application URL & Paths
    |--------------------------------------------------------------------------
    */
    'url' => env('MIX_URL', 'http://localhost/'),
    'path' => env('MIX_PATH', '/var/www/bestofxyz/public_html/'),
    'domain' => env('MIX_DOMAIN_NAME', 'https://bestofxyz.com'),
    'host_name' => env('MIX_HOST_NAME', 'LOCAL'),
    'timezone' => env('MIX_TIME_ZONE', 'UTC'),

    /*
    |--------------------------------------------------------------------------
    | Static Asset URL
    |--------------------------------------------------------------------------
    |
    | Base URL for static assets (images, fonts, etc.). Empty string serves
    | from origin. Set to CDN URL in production (e.g. "https://cdn.bestofxyz.com").
    |
    */
    'asset_url' => env('MIX_ASSET_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | Static Asset Version
    |--------------------------------------------------------------------------
    */
    'static_version' => env('MIX_STATIC_VERSION', '202408101734'),

    /*
    |--------------------------------------------------------------------------
    | Debugging
    |--------------------------------------------------------------------------
    */
    'debug' => env('MIX_DEBUGGING', false),
    'debug_bar' => env('MIX_DEBUGGING_BAR', false),
    'debug_strict_mode' => env('MIX_DEBUGGING_STRICT_MODE', false),

    /*
    |--------------------------------------------------------------------------
    | Encryption
    |--------------------------------------------------------------------------
    */
    'encryption' => [
        'key' => env('MIX_KEY'),
        'first_key' => env('MIX_FKEY'),
        'second_key' => env('MIX_SKEY'),
        'method' => 'aes-256-cbc',
        'hash' => 'sha3-512',
    ],

    /*
    |--------------------------------------------------------------------------
    | Session
    |--------------------------------------------------------------------------
    |
    | driver: 'redis' (default) or 'native' (PHP file-based fallback).
    | If driver is 'redis' but Redis is unavailable, falls back to native.
    | lifetime: session TTL in seconds (default 7200 = 2 hours).
    | prefix: Redis key prefix for session data.
    |
    */
    'strict_policy' => true,
    'session' => [
        'driver' => env('MIX_SESSION_DRIVER', 'redis'),
        'lifetime' => (int) env('MIX_SESSION_LIFETIME', 7200),
        'prefix' => 'pksess:',
    ],

    /*
    |--------------------------------------------------------------------------
    | Security
    |--------------------------------------------------------------------------
    */
    'master_password' => env('MIX_MSTRPASS'),

    /*
    |--------------------------------------------------------------------------
    | Rotation URL
    |--------------------------------------------------------------------------
    */
    
];
