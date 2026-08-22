<?php

declare(strict_types=1);

namespace Framework\Providers;

use Framework\Support\ServiceProvider;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class RequestServiceProvider implements ServiceProvider
{
    public function register(Container $container): void
    {
        $request = Request::capture();

        $container->instance('request', $request);
        $container->instance(Request::class, $request);
        $container->alias(Request::class, SymfonyRequest::class);
    }
}
