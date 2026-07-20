<?php

/** Strict typing */

declare(strict_types=1);

require dirname(__DIR__, 2) . '/vendor/autoload.php';
require __DIR__ . '/loader.php';

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;

date_default_timezone_set('UTC');

global $container, $events;
$container = new Container();
$events = new Dispatcher($container);
