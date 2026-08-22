<?php

declare(strict_types=1);

namespace Framework\Support;

use Illuminate\Container\Container;

interface ServiceProvider
{
    public function register(Container $container): void;
}
