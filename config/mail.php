<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mail Driver
    |--------------------------------------------------------------------------
    |
    | Supported: 'smtp', 'log'
    |
    */
    'driver' => env('MAIL_DRIVER', 'log'),

    /*
    |--------------------------------------------------------------------------
    | Mail Library
    |--------------------------------------------------------------------------
    |
    | Options: 'SymfonyMailer'
    |
    */
    'library' => 'SymfonyMailer', 

    /*
    |--------------------------------------------------------------------------
    | SMTP Configuration
    |--------------------------------------------------------------------------
    */
    'smtp' => [
        'host' => env('MAIL_HOST', 'smtp.sendgrid.net'),
        'port' => (int) env('MAIL_PORT', 587),
        'username' => env('MAIL_USERNAME'),
        'password' => env('MAIL_PASSWORD'),
        'secure' => env('MAIL_ENCRYPTION', 'tls'),
        'sender' => env('MAIL_FROM_ADDRESS'),
        'reply_to' => env('MAIL_REPLY_TO'),
        'admin_email' => env('MAIL_ADMIN_EMAIL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | MailCatcher (Local Development)
    |--------------------------------------------------------------------------
    */
    'mailcatcher' => [
        'host' => '127.0.0.1',
        'port' => 1025,
    ],

];
