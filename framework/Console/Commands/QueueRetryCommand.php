<?php

namespace Framework\Console\Commands;

use App\Models\FailedJob;
use App\Models\MessageQueueIntegration;
use App\Models\MessageQueueRiskCalculation;
use App\Models\MessageQueueWebhooks;
use Framework\Console\BaseCommand;
use Mailer\Models\MessageQueueTrigger;

/**
 * Retry a permanently failed queue job by resetting it for reprocessing.
 *
 * Resets the original queue row (attempts = 0, status = pending, next_retry_at = null)
 * and marks the failed_jobs record as retried. Supports retrying by ID or all jobs
 * for a specific queue.
 */
class QueueRetryCommand extends BaseCommand
{
    /** @var array Maps queue names to their model class and status column. */
    private const QUEUE_MAP = [
        'email'       => ['model' => MessageQueueTrigger::class,          'status' => 'is_sent'],
        'integration' => ['model' => MessageQueueIntegration::class,      'status' => 'is_sent'],
        'webhook'     => ['model' => MessageQueueWebhooks::class,         'status' => 'is_sent'],
        'risk'        => ['model' => MessageQueueRiskCalculation::class,  'status' => 'is_processed'],
    ];

    /**
     * Get the command signature.
     *
     * @return string
     */
    public function signature(): string
    {
        return 'queue:retry';
    }

    /**
     * Get the command description for help output.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Retry a failed job (--id=123) or all failed jobs for a queue (--queue=email)';
    }

    /**
     * Get detailed help text for this command.
     *
     * @return string
     */
    public function help(): string
    {
        return <<<'HELP'
  Usage:
    php artisan queue:retry [options]

  Options:
    --id=<N>        Retry a single failed job by its failed_jobs ID
    --queue=<name>  Retry all un-retried failed jobs for a queue
                    Valid queues: email, integration, webhook, risk

  Description:
    Resets a permanently failed job for reprocessing by clearing its
    attempts, status, next_retry_at, and error_message. Marks the
    failed_jobs record with a retried_at timestamp. Already-retried
    jobs are skipped.

  Examples:
    php artisan queue:retry --id=123        Retry failed job #123
    php artisan queue:retry --queue=email   Retry all failed email jobs
    php artisan queue:retry --queue=webhook Retry all failed webhook jobs
HELP;
    }

    /**
     * Execute the command.
     *
     * @param array $args CLI arguments.
     *
     * @return int Exit code (0 = success, 1 = failure).
     */
    public function handle(array $args): int
    {
        $options = $this->parseOptions($args);
        $id = $this->option($options, 'id');
        $queue = $this->option($options, 'queue');

        if (!$id && !$queue) :
            $this->error('Usage: php artisan queue:retry --id=123  OR  php artisan queue:retry --queue=email');
            return 1;
        endif;

        if ($id) :
            return $this->retryById((int) $id);
        endif;

        return $this->retryByQueue($queue);
    }

    /**
     * Retry a single failed job by its failed_jobs.id.
     *
     * @param int $id The failed_jobs record ID.
     *
     * @return int Exit code.
     */
    private function retryById(int $id): int
    {
        $failedJob = FailedJob::find($id);

        if (!$failedJob) :
            $this->error("Failed job #{$id} not found.");
            return 1;
        endif;

        if ($failedJob->retried_at) :
            $this->warn("Failed job #{$id} was already retried at {$failedJob->retried_at}.");
            return 1;
        endif;

        $retried = $this->resetOriginalJob($failedJob);

        if (!$retried) :
            $this->error("Could not find original job #{$failedJob->job_id} in the {$failedJob->queue} queue.");
            return 1;
        endif;

        $failedJob->retried_at = date('Y-m-d H:i:s');
        $failedJob->save();

        $this->success("Retried failed job #{$id} (queue: {$failedJob->queue}, original job: #{$failedJob->job_id})");

        return 0;
    }

    /**
     * Retry all un-retried failed jobs for a specific queue.
     *
     * @param string $queue The queue name (email, integration, webhook, risk).
     *
     * @return int Exit code.
     */
    private function retryByQueue(string $queue): int
    {
        if (!isset(self::QUEUE_MAP[$queue])) :
            $this->error("Unknown queue: {$queue}. Valid queues: " . implode(', ', array_keys(self::QUEUE_MAP)));
            return 1;
        endif;

        $failedJobs = FailedJob::where('queue', $queue)
            ->whereNull('retried_at')
            ->get();

        if ($failedJobs->isEmpty()) :
            $this->success("No un-retried failed jobs for the '{$queue}' queue.");
            return 0;
        endif;

        $count = 0;

        foreach ($failedJobs as $failedJob) :
            $retried = $this->resetOriginalJob($failedJob);

            if ($retried) :
                $failedJob->retried_at = date('Y-m-d H:i:s');
                $failedJob->save();
                $count++;
            endif;
        endforeach;

        $this->success("Retried {$count}/{$failedJobs->count()} failed jobs for the '{$queue}' queue.");

        return 0;
    }

    /**
     * Reset the original queue job so it becomes eligible for reprocessing.
     *
     * Sets attempts = 0, status = pending (0), clears next_retry_at and error_message.
     *
     * @param FailedJob $failedJob The failed_jobs record with queue and job_id.
     *
     * @return bool True if the original job was found and reset, false otherwise.
     */
    private function resetOriginalJob(FailedJob $failedJob): bool
    {
        $config = self::QUEUE_MAP[$failedJob->queue] ?? null;

        if (!$config) :
            return false;
        endif;

        $modelClass = $config['model'];
        $statusColumn = $config['status'];

        $originalJob = $modelClass::find($failedJob->job_id);

        if (!$originalJob) :
            return false;
        endif;

        $originalJob->$statusColumn = 0;
        $originalJob->attempts = 0;
        $originalJob->next_retry_at = null;
        $originalJob->error_message = null;
        $originalJob->save();

        return true;
    }
}
