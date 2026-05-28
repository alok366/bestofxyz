<?php

use App\Controllers\DeveloperApiController;

$router->group(['prefix' => '/api/v4/integration', 'middleware' => ['developer.api']], function ($router) {
    $router->post('/', [DeveloperApiController::class, 'handle']);
});