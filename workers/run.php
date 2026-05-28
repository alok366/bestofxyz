<?php

/**
 * Worker Runner & Manager — Single Entry Point
 *
 * Run a worker (Supervisor / foreground):
 *   php workers/run.php email
 *   php workers/run.php risk
 *
 * Manage workers (local development):
 *   php workers/run.php email start
 *   php workers/run.php email stop
 *   php workers/run.php email restart
 *   php workers/run.php email status
 *
 * Supervisor config:
 *   command=/usr/bin/php /path/to/workers/run.php email
 *   autostart=true
 *   autorestart=true
 */

if (php_sapi_name() !== 'cli') :
    die("This script must be run from the command line.");
endif;

$workers = [
    'email'      => \App\Workers\EmailWorker::class,
    'risk'       => \App\Workers\RiskWorker::class,
    'subscriber' => \App\Workers\SubscriberSideEffectsWorker::class,
];

$name = $argv[1] ?? null;
$action = $argv[2] ?? null;

if (!$name || !isset($workers[$name])) :
    echo "Usage: php workers/run.php <worker> [action]\n\n";
    echo "Workers: " . implode(', ', array_keys($workers)) . "\n\n";
    echo "Run (foreground / Supervisor):\n";
    echo "  php workers/run.php email\n\n";
    echo "Manage (local dev):\n";
    echo "  php workers/run.php email start|stop|restart|status\n";
    exit(1);
endif;

// Management actions (start/stop/restart/status) — local dev only
if ($action) :
    require_once __DIR__ . '/WorkerManager.php';
    $manager = new WorkerManager($name);

    match($action) {
        'start'   => $manager->start(),
        'stop'    => $manager->stop(),
        'restart' => $manager->restart(),
        'status'  => $manager->status(),
        default   => print("Unknown action: $action. Use: start, stop, restart, status\n"),
    };
    exit(0);
endif;

// Run the worker in foreground (Supervisor mode)
require_once __DIR__ . '/../framework/Bootstrap/cli.php';

use App\Workers\BaseWorker;

$workerClass = $workers[$name];
BaseWorker::boot(new $workerClass());
