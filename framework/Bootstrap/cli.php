<?php

/**
 * CLI Bootstrap
 *
 * Minimal bootstrap for all command-line entry points: artisan, workers, cron jobs.
 * Loads autoloader, environment, config, Eloquent, container, and events.
 * Does NOT load sessions, Tracy, CSRF, or any web-only globals.
 *
 * Usage:
 *   require __DIR__ . '/framework/Bootstrap/cli.php';
 *
 * For commands that need (email rendering in cron jobs):
 *   require __DIR__ . '/framework/Bootstrap/cli.php';
 */

declare(strict_types=1);

namespace Framework\Bootstrap;

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;

class CliBootstrap
{
    private static bool $booted = false;

    /**
     * Boot the CLI environment.
     *
     * Loads autoloader, .env, config, Eloquent, container, and events.
     *
     * @param string|null $basePath Override base path (for staging/production where paths differ).
     *
     * @return void
     */
    public static function boot(?string $basePath = null): void
    {
        if (self::$booted) :
            return;
        endif;

        $basePath = $basePath ?? dirname(__DIR__, 2);

        require_once $basePath . '/vendor/autoload.php';

        $dotenv = \Dotenv\Dotenv::createImmutable($basePath, '.env');
        $dotenv->load();

        \Framework\Services\ConfigService::load($basePath . '/config');

        date_default_timezone_set(config('app.timezone', 'UTC'));

        // Database (Eloquent Capsule)
        require $basePath . '/framework/Bootstrap/database.php';

        // IoC Container + Event Dispatcher
        global $container, $events;
        $container = new Container();
        $events = new Dispatcher($container);

        // Register event/listener bindings
        require $basePath . '/framework/Bootstrap/events.php';

        // Set DOCUMENT_ROOT for services that reference it in CLI context
        if (!isset($_SERVER['DOCUMENT_ROOT'])) :
            $_SERVER['DOCUMENT_ROOT'] = $basePath . '/public';
        endif;

        self::$booted = true;
    }
}

// Auto-boot when this file is required
CliBootstrap::boot();
