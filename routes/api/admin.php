<?php

use App\Controllers\AdminDashboardController;
use App\Controllers\HealthController;
use App\Controllers\HelpController;

/**
 * Stateless Admin API — JWT-authenticated, no envelope.
 * All endpoints return Response objects via $this->response->ok() / ->problem().
 */
$router->group(['prefix' => '/api/v4/admin', 'middleware' => ['cors', 'start.session', 'auth.jwt', 'auth.admin']], function ($router) {

    /** Dashboard Stats */
    $router->get('/stats/user-activity', [AdminDashboardController::class, 'userActivity']);
    $router->get('/stats/dashboard-widgets', [AdminDashboardController::class, 'dashboardWidgets']);
    $router->get('/stats/plans-vs-users', [AdminDashboardController::class, 'plansVsUsers']);

    /** Help Docs */
    $router->get('/help-docs', [HelpController::class, 'index']);
    $router->get('/help-docs/{id}', [HelpController::class, 'showAdmin'])->where('id', '[a-zA-Z0-9\-]+');
    $router->post('/help-docs', [HelpController::class, 'store']);
    $router->put('/help-docs/{id}', [HelpController::class, 'update'])->where('id', '[a-zA-Z0-9\-]+');
    $router->delete('/help-docs/{id}', [HelpController::class, 'destroy'])->where('id', '[a-zA-Z0-9\-]+');

    /** System Status */
    $router->get('/status', [HealthController::class, 'status']);
});
