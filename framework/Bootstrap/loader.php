<?php

if(php_sapi_name() !== 'cli'):
    error_reporting($_SERVER['HTTP_HOST'] == 'bestofxyz.local'); 
endif;

/*
|--------------------------------------------------------------------------
| Defining constants
|--------------------------------------------------------------------------
*/

$dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__, 2), '.env');
$dotenv->load();

/*
|--------------------------------------------------------------------------
| Load Configuration
|--------------------------------------------------------------------------
*/
\Framework\Services\ConfigService::load(dirname(__DIR__, 2) . '/config');

define('HOST_NAME', config('app.host_name'));

/*
|--------------------------------------------------------------------------
| Session Note
|--------------------------------------------------------------------------
| Session start, CSRF token, and cookie params are handled by
| StartSessionMiddleware — not here. Entry points must NOT call
| session_start() directly.
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| Initializing Tracy for Development Environment
|--------------------------------------------------------------------------
*/

if (config('app.debug') && !defined('DISABLE_TRACY')) :
    //ini_set('display_errors', true);
    //ini_set('display_startup_errors', true);
    \Tracy\Debugger::setSessionStorage(new \Tracy\NativeSession);
    \Tracy\Debugger::enable();
    \Tracy\Debugger::$showBar = (bool)config('app.debug_bar');
    \Tracy\Debugger::$strictMode = (bool)config('app.debug_strict_mode');
endif;

/*
|--------------------------------------------------------------------------
| Illuminate Capsule
|--------------------------------------------------------------------------
*/
require __DIR__ . '/database.php';

$userResolver = function () {
    global $container;
    $userData = [
        'authenticated' => false,
    ];

    // Check if auth service is available (not available in CLI/cron context)
    if ($container->bound('auth')) {
        $auth = $container->make('auth');
        if ($auth->check()) {
            $userInfo = $auth->user();
            $userData['authenticated'] = true;
            $userData['userName'] = $auth->getName();
            $userData['privacy'] = $userInfo && $userInfo->privacy ? 'data-privacy' : '';
            $userData['userID'] = $userInfo->id ?? 0;
            $userData['userEmail'] = $userInfo->email ?? '';
            $userData['userType'] = $userInfo->userType ?? 'C';
        }
    }

    return $userData;
};

$sessionActive = session_status() === PHP_SESSION_ACTIVE;
$gdprAccepted = $sessionActive && ((!empty($_SESSION['login']->gdpr)) || (!empty($_SESSION['gdprok'])));
