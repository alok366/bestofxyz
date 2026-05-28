<?php

/**
 * Worker process manager for local development.
 *
 * Handles start/stop/restart/status for any worker registered in run.php.
 * On staging/production, Supervisor handles this instead.
 */
class WorkerManager
{
    private string $name;
    private string $script;
    private string $pidFile;

    /**
     * @param string $name Worker name as registered in run.php (e.g., "email", "risk").
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->script = __DIR__ . "/run.php $name";
        $this->pidFile = __DIR__ . "/$name-worker.pid";
    }

    /**
     * Start the worker as a background process.
     *
     * @return void
     */
    public function start(): void
    {
        if ($this->isRunning()) :
            echo "{$this->name} worker is already running (PID: {$this->getPid()})\n";
            return;
        endif;

        $command = "php {$this->script} > /dev/null 2>&1 & echo $!";
        $pid = shell_exec($command);

        if ($pid) :
            file_put_contents($this->pidFile, trim($pid));
            echo "{$this->name} worker started (PID: " . trim($pid) . ")\n";
        else :
            echo "Failed to start {$this->name} worker\n";
        endif;
    }

    /**
     * Stop the worker via SIGTERM.
     *
     * @return void
     */
    public function stop(): void
    {
        if (!$this->isRunning()) :
            echo "{$this->name} worker is not running\n";
            return;
        endif;

        $pid = $this->getPid();

        if (posix_kill($pid, SIGTERM)) :
            unlink($this->pidFile);
            echo "{$this->name} worker stopped (PID: $pid)\n";
        else :
            echo "Failed to stop {$this->name} worker (PID: $pid)\n";
        endif;
    }

    /**
     * Restart the worker (stop + start with 2s delay).
     *
     * @return void
     */
    public function restart(): void
    {
        $this->stop();
        sleep(2);
        $this->start();
    }

    /**
     * Show worker status.
     *
     * @return void
     */
    public function status(): void
    {
        if ($this->isRunning()) :
            echo "{$this->name} worker is running (PID: {$this->getPid()})\n";
        else :
            echo "{$this->name} worker is not running\n";
        endif;
    }

    /**
     * Check if the worker process is alive.
     *
     * @return bool
     */
    private function isRunning(): bool
    {
        $pid = $this->getPid();
        return $pid && posix_kill($pid, 0);
    }

    /**
     * Read the PID from the PID file.
     *
     * @return int|null
     */
    private function getPid(): ?int
    {
        if (!file_exists($this->pidFile)) :
            return null;
        endif;

        return (int) file_get_contents($this->pidFile);
    }
}
