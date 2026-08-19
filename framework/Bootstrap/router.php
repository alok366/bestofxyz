<?php

use Illuminate\Routing\Router;
use Framework\Http\Middleware\CorsMiddleware;
use Framework\Http\Middleware\AuthenticateAdminMiddleware;
use Framework\Http\Middleware\ApiMiddleware;
use Framework\Http\Middleware\StartSessionMiddleware;
use Framework\Http\Middleware\LoginThrottleMiddleware;
use Framework\Http\Middleware\JwtAuthMiddleware;
use Framework\Http\Middleware\VoteRateLimitMiddleware;

global $container, $events;


$container->singleton('auth', function() {
    return new \App\Services\AuthService();
});


$router = new Router($events, $container);
// Register CORS middleware
$router->aliasMiddleware('cors', CorsMiddleware::class);
$router->aliasMiddleware('auth.admin', AuthenticateAdminMiddleware::class);
$router->aliasMiddleware('developer.api', ApiMiddleware::class);
$router->aliasMiddleware('start.session', StartSessionMiddleware::class);
$router->aliasMiddleware('auth.throttle', LoginThrottleMiddleware::class);
$router->aliasMiddleware('auth.jwt', JwtAuthMiddleware::class);
$router->aliasMiddleware('vote.rate', VoteRateLimitMiddleware::class);
return $router;

