<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection
    |--------------------------------------------------------------------------
    */
    'driver' => 'mysql',
    'host' => env('MIX_DB_HOST', 'localhost'),
    'database' => env('MIX_DB_NAME', 'bestofxyz'),
    'username' => env('MIX_DB_USER', 'root'),
    'password' => env('MIX_DB_PASS', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',

];
