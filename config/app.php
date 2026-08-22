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
    'env' => env('APP_ENV', 'development'),

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
    'url' => env('APP_URL', 'http://localhost/'),
    'path' => env('APP_PATH', '/var/www/bestofxyz/public_html/'),
    'domain' => env('APP_DOMAIN', 'https://bestofxyz.com'),
    'host_name' => env('APP_HOST_NAME', 'LOCAL'),
    'timezone' => env('APP_TIMEZONE', 'UTC'),

    /*
    |--------------------------------------------------------------------------
    | Static Asset URL
    |--------------------------------------------------------------------------
    |
    | Base URL for static assets (images, fonts, etc.). Empty string serves
    | from origin. Set to CDN URL in production (e.g. "https://cdn.bestofxyz.com").
    |
    */
    'asset_url' => env('APP_ASSET_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | Static Asset Version
    |--------------------------------------------------------------------------
    */
    'static_version' => env('APP_STATIC_VERSION', '202408101734'),

    /*
    |--------------------------------------------------------------------------
    | Debugging
    |--------------------------------------------------------------------------
    */
    'debug' => env('APP_DEBUG', false),
    'debug_bar' => env('APP_DEBUG_BAR', false),
    'debug_strict_mode' => env('APP_DEBUG_STRICT_MODE', false),

    /*
    |--------------------------------------------------------------------------
    | Encryption
    |--------------------------------------------------------------------------
    */
    'encryption' => [
        'key' => env('APP_KEY'),
        'first_key' => env('APP_FIRST_KEY'),
        'second_key' => env('APP_SECOND_KEY'),
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
        'driver' => env('SESSION_DRIVER', 'redis'),
        'lifetime' => (int) env('SESSION_LIFETIME', 7200),
        'prefix' => 'pksess:',
    ],

    /*
    |--------------------------------------------------------------------------
    | Security
    |--------------------------------------------------------------------------
    */
    'master_password' => env('APP_MASTER_PASSWORD'),

];
