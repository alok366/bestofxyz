<?php

namespace Framework\Console\Commands;

use Framework\Console\BaseCommand;

/**
 * Manage background workers for local development.
 *
 * Wraps the WorkerManager to start/stop/restart workers or check their status
 * via `php artisan worker:<action>`. On production, Supervisor handles workers
 * directly via workers/run.php.
 */
class WorkerCommand extends BaseCommand
{
    /**
     * Available workers — mirrors the registry in workers/run.php.
     *
     * @var array<string, string>
     */
    private array $workers = [
        'email'      => \App\Workers\EmailWorker::class,
        'risk'       => \App\Workers\RiskWorker::class,
        'subscriber' => \App\Workers\SubscriberSideEffectsWorker::class,
    ];

    /**
     * Get the command signature.
     *
     * @return string
     */
    public function signature(): string
    {
        return 'worker';
    }

    /**
     * Get the command description.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Manage background workers (start, stop, restart, status)';
    }

    /**
     * Get detailed help text for this command.
     *
     * @return string
     */
    public function help(): string
    {
        $workerNames = implode(', ', array_keys($this->workers));

        return <<<HELP
  Usage:
    php artisan worker <action> <name>
    php artisan worker <action> --all

  Actions:
    start     Start a worker as a background process
    stop      Stop a running worker via SIGTERM
    restart   Stop then start a worker
    status    Show whether a worker is running

  Workers:
    {$workerNames}

  Options:
    --all     Apply action to all workers

  Examples:
    php artisan worker start email
    php artisan worker stop risk
    php artisan worker start subscriber
    php artisan worker status subscriber
    php artisan worker restart --all
    php artisan worker status --all
HELP;
    }

    /**
     * Handle the worker command.
     *
     * @param array $args CLI arguments: <action> <name> or <action> --all.
     * @return int Exit code.
     */
    public function handle(array $args): int
    {
        $options = $this->parseOptions($args);
        $positional = array_values(array_filter($args, fn($a) => !str_starts_with($a, '-')));

        $action = $positional[0] ?? null;
        $name = $positional[1] ?? null;
        $all = $this->option($options, 'all', false);

        if (!$action || !in_array($action, ['start', 'stop', 'restart', 'status'])) :
            $this->error("Action required: start, stop, restart, or status.");
            $this->line($this->help());
            return 1;
        endif;

        if (!$name && !$all) :
            $this->error("Specify a worker name or use --all.");
            $this->line("Available workers: " . implode(', ', array_keys($this->workers)));
            return 1;
        endif;

        $targets = $all ? array_keys($this->workers) : [$name];

        foreach ($targets as $target) :
            if (!isset($this->workers[$target])) :
                $this->error("Unknown worker: {$target}. Available: " . implode(', ', array_keys($this->workers)));
                return 1;
            endif;

            $this->runAction($action, $target);
        endforeach;

        return 0;
    }

    /**
     * Execute a management action on a single worker.
     *
     * @param string $action One of start, stop, restart, status.
     * @param string $name Worker name (e.g. "email").
     * @return void
     */
    private function runAction(string $action, string $name): void
    {
        require_once __DIR__ . '/../../../workers/WorkerManager.php';

        $manager = new \WorkerManager($name);

        match($action) {
            'start'   => $manager->start(),
            'stop'    => $manager->stop(),
            'restart' => $manager->restart(),
            'status'  => $manager->status(),
        };
    }
}
