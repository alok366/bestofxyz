<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cache Driver
    |--------------------------------------------------------------------------
    |
    | Default cache driver for the application.
    | Supported: 'redis', 'file', 'null'
    |
    */
    'driver' => env('MIX_CACHE_DRIVER', 'redis'),

    /*
    |--------------------------------------------------------------------------
    | Redis Configuration
    |--------------------------------------------------------------------------
    */
    'redis' => [
        'enabled' => env('MIX_REDIS_ENABLED', true),
        'host' => env('MIX_REDIS_HOST', '127.0.0.1'),
        'port' => 6379,
        'password' => env('MIX_REDIS_PASSWORD'),
        'prefix' => 'app',
    ],

];
