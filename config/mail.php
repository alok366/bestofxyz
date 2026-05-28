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
    'driver' => env('MIX_MAIL_DRIVER', 'log'),

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
        'host' => env('MIX_SMTP_HOST', 'smtp.sendgrid.net'),
        'port' => '587',
        'username' => env('MIX_SMTP_USER'),
        'password' => env('MIX_SMTP_PASS'),
        'secure' => 'tls',
        'sender' => env('MIX_SMTP_SENDER'),
        'reply_to' => env('MIX_SMTP_REPLY_TO'),
        'admin_email' => env('MIX_SMTP_ADMIN_EMAIL'),
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
