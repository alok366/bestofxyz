<?php

declare(strict_types=1);

namespace Framework\Providers;

use App\Services\AuthService;
use Framework\Support\ServiceProvider;
use Illuminate\Container\Container;

class AuthServiceProvider implements ServiceProvider
{
    public function register(Container $container): void
    {
        $container->singleton('auth', function () {
            return new AuthService();
        });
    }
}
