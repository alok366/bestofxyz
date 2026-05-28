<?php

namespace App\Workers;

use Utils\BMLogger;

abstract class BaseWorker
{
    /** @var bool Flag for graceful shutdown via SIGTERM/SIGINT. */
    private bool $running = true;

    /** @var int Maximum seconds before automatic restart. */
    protected int $maxExecutionTime = 9000;

    /** @var int Seconds to sleep when no jobs are available. */
    protected int $sleepInterval = 2;

    /** @var int Maximum memory in bytes before automatic restart. */
    protected int $maxMemoryUsage;

    /**
     * Initialize the worker with resource limits and register signal handlers.
     *
     * @param int $maxExecutionTime Maximum seconds before restart (default 900 = 15 min).
     * @param int $sleepInterval Seconds to sleep when idle (default 2).
     * @param int $maxMemoryMb Maximum memory in MB before restart (default 128).
     */
    public function __construct(int $maxExecutionTime = 900, int $sleepInterval = 2, int $maxMemoryMb = 128)
    {
        $this->maxExecutionTime = $maxExecutionTime;
        $this->sleepInterval = $sleepInterval;
        $this->maxMemoryUsage = $maxMemoryMb * 1024 * 1024;

        pcntl_signal(SIGTERM, [$this, 'shutdown']);
        pcntl_signal(SIGINT, [$this, 'shutdown']);
    }

    /**
     * The worker's name for logging (e.g., "Email worker", "Risk worker").
     *
     * @return string
     */
    abstract protected function name(): string;

    /**
     * The worker's key used for PID file naming (e.g., "email", "risk").
     *
     * @return string
     */
    abstract protected function key(): string;

    /**
     * Process a single iteration of work.
     *
     * Return true if work was done (skip sleep), false if idle (will sleep).
     *
     * @return bool Whether any work was processed.
     */
    abstract protected function process(): bool;

    /**
     * Run the worker loop.
     *
     * Continuously calls process() until a shutdown signal is received,
     * memory limit is reached, or max execution time expires.
     * Supervisor will automatically restart the process after exit.
     *
     * @return void
     */
    public function run(): void
    {
        $startTime = time();
        $this->writePidFile();

        BMLogger::log([
            'message' => $this->name() . ' started',
            'pid' => getmypid(),
            'timestamp' => date('Y-m-d H:i:s')
        ]);

        while ($this->running) :
            pcntl_signal_dispatch();

            if (memory_get_usage() > $this->maxMemoryUsage) :
                BMLogger::log([
                    'message' => 'Memory limit reached, restarting',
                    'worker' => $this->name(),
                    'memory_usage' => memory_get_usage(),
                    'timestamp' => date('Y-m-d H:i:s')
                ]);
                break;
            endif;

            if ((time() - $startTime) > $this->maxExecutionTime) :
                BMLogger::log([
                    'message' => 'Max execution time reached, restarting',
                    'worker' => $this->name(),
                    'runtime' => time() - $startTime,
                    'timestamp' => date('Y-m-d H:i:s')
                ]);
                break;
            endif;

            try {
                $didWork = $this->process();

                if (!$didWork) :
                    sleep($this->sleepInterval);
                endif;
            } catch (\Exception $e) {
                BMLogger::error($e, static::class . '::process');
                sleep(5);
            }
        endwhile;

        $this->removePidFile();

        BMLogger::log([
            'message' => $this->name() . ' stopped',
            'pid' => getmypid(),
            'runtime' => time() - $startTime,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Write current process PID to file for health check monitoring.
     *
     * @return void
     */
    private function writePidFile(): void
    {
        $pidFile = $this->getPidFilePath();
        file_put_contents($pidFile, (string) getmypid());
    }

    /**
     * Remove PID file on worker shutdown.
     *
     * @return void
     */
    private function removePidFile(): void
    {
        $pidFile = $this->getPidFilePath();

        if (file_exists($pidFile)) :
            @unlink($pidFile);
        endif;
    }

    /**
     * Get the PID file path for this worker.
     *
     * @return string Absolute path to the PID file.
     */
    private function getPidFilePath(): string
    {
        return dirname(__DIR__, 3) . '/workers/' . $this->key() . '-worker.pid';
    }

    /**
     * Signal handler for graceful shutdown.
     *
     * @return void
     */
    public function shutdown(): void
    {
        $this->running = false;
        BMLogger::log([
            'message' => $this->name() . ' shutdown signal received',
            'pid' => getmypid(),
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Boot and run the worker with fatal error handling.
     *
     * Call this from the entry point script. Catches fatal exceptions
     * and exits with proper code for Supervisor.
     *
     * @param static $worker
     *
     * @return never
     */
    public static function boot(self $worker): never
    {
        try {
            $worker->run();
            exit(0);
        } catch (\Exception $e) {
            BMLogger::error($e, static::class . '::boot');
            exit(1);
        }
    }
}
