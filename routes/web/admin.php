<?php

/** @var \Illuminate\Routing\Router $router */

use App\Controllers\AdminDashboardController;

/**
 * Admin SPA — single catch-all route.
 * All admin pages are rendered client-side by Preact.
 */
$router->group(['middleware' => ['start.session', 'auth.admin'], 'prefix' => 'admin'], function ($router) {
    $router->get('/{any?}', [AdminDashboardController::class, 'spa'])->where('any', '.*');
});


