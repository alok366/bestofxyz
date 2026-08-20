<?php

global $router;

$router->group(['prefix' => 'api', 'middleware' => ['cors']], function ($router) {

    // ── Public read endpoints (no auth required) ───────────────────
    $router->get('/categories',                              'App\Controllers\Api\CategoryController@index');
    $router->get('/categories/{slug}',                       'App\Controllers\Api\CategoryController@show');
    $router->get('/categories/{catSlug}/resources/{resSlug}', 'App\Controllers\Api\ResourceController@show');
    $router->get('/resources/{slug}',                        'App\Controllers\Api\ResourceController@showBySlug');
    $router->get('/pending/{slug}',                          'App\Controllers\Api\PendingController@show');
    $router->get('/resources/{id}/comments',                 'App\Controllers\Api\CommentController@index');
    $router->get('/tags',                                    'App\Controllers\Api\TagController@index');

    // ── Protected write endpoints (JWT auth required) ──────────────
    $router->group(['middleware' => ['start.session', 'auth.jwt']], function ($router) {
        $router->post('/resources',               'App\Controllers\Api\ResourceController@store');
        $router->post('/resources/{id}/vote',     ['middleware' => ['vote.rate'], 'uses' => 'App\Controllers\Api\VoteController@store']);
        $router->delete('/resources/{id}/vote',   'App\Controllers\Api\VoteController@destroy');
        $router->post('/resources/{id}/comments', 'App\Controllers\Api\CommentController@store');
        $router->post('/comments/{id}/vote',      ['middleware' => ['vote.rate'], 'uses' => 'App\Controllers\Api\CommentVoteController@store']);
    });

    // ── Auth endpoints (issue/refresh tokens) ──────────────────────
    $router->group(['middleware' => ['start.session']], function ($router) {
        $router->post('/auth/register', 'App\Controllers\Api\AuthController@register');
        $router->post('/auth/login',    'App\Controllers\Api\AuthController@login');
        $router->post('/auth/refresh',  'App\Controllers\Api\AuthController@refresh');
        $router->post('/auth/logout',   'App\Controllers\Api\AuthController@logout');
    });
});