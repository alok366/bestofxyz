<?php

/** Strict typing */

declare(strict_types=1);

require dirname(__DIR__, 2) . '/vendor/autoload.php';
require __DIR__ . '/loader.php';

use Framework\Services\TwigService;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;

date_default_timezone_set('UTC');

// Set Twig globals before initialization
TwigService::setGlobals($twigGlobalData, $userResolver);
$twig = TwigService::getInstance();

global $container, $events;
$container = new Container();
$events = new Dispatcher($container);
