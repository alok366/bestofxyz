<?php

/** @var \Illuminate\Routing\Router $router */

use App\Controllers\UserSpaController;

/**
 * SPA catch-all — all web paths render the React SPA shell.
 * React Router owns URL matching client-side.
 */
$router->get('/{any?}', [UserSpaController::class, 'renderApp'])->where('any', '.*');
