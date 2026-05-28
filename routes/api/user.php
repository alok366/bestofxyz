<?php

use App\Controllers\AuthController;


/**
 * JWT Authentication Endpoints
 * Login uses session + rate limiting (same as form login).
 * Refresh and logout require a valid token (no session needed).
 */
$router->group(['prefix' => '/auth', 'middleware' => ['cors', 'start.session', 'auth.throttle:5,2']], function ($router) {
    $router->post('/token', [AuthController::class, 'issueToken']);
});

$router->group(['prefix' => '/auth', 'middleware' => ['cors', 'start.session', 'auth.jwt']], function ($router) {
    $router->post('/refresh', [AuthController::class, 'refreshToken']);
    $router->post('/logout', [AuthController::class, 'revokeToken']);
});

