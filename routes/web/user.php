<?php

/** @var \Illuminate\Routing\Router $router */

use App\Controllers\AuthController;
use App\Controllers\ErrorController;
use App\Controllers\UserSpaController;

/**
 * SPA catch-all — all other authenticated paths render the User SPA
 * shell. Preact-router owns URL matching client-side; unknown paths
 * fall through to the NotFound component.
 */
$router->get('/{any?}', [UserSpaController::class, 'shell'])->where('any', '.*');
$router->get('/logout', [AuthController::class, 'logout'])->middleware('start.session');

/**
 * Authenticated web routes. Most paths are served by the User SPA shell
 * walkthrough, redirect handlers, and file exports stay as server-side
 * routes because they use a different twig layout or return non-HTML.
 */
$router->group(['middleware' => ['start.session', 'csrf']], function ($router) {});


$router->group(['middleware' => ['start.session', 'cors', 'csrf', 'auth.throttle:5,2']], function ($router) {
    $router->post('/login', [AuthController::class, 'login']);
    $router->post('/master/login', [AuthController::class, 'masterLogin']);
});

$router->get('/error/403', [ErrorController::class, 'show403']);
$router->get('/error/500', [ErrorController::class, 'show500']);
