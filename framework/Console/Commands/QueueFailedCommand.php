<?php

namespace Framework\Console\Commands;

use App\Models\FailedJob;
use Framework\Console\BaseCommand;

/**
 * List all permanently failed queue jobs.
 *
 * Displays the failed_jobs table contents with optional queue filtering.
 * Useful for debugging delivery failures across email, integration,
 * webhook, and risk queues.
 */
class QueueFailedCommand extends BaseCommand
{
    /**
     * Get the command signature.
     *
     * @return string
     */
    public function signature(): string
    {
        return 'queue:failed';
    }

    /**
     * Get the command description for help output.
     *
     * @return string
     */
    public function description(): string
    {
        return 'List all permanently failed queue jobs (--queue=email to filter)';
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
    php artisan queue:failed [options]

  Options:
    --queue=<name>  Filter by queue name (email, integration, webhook, risk)

  Description:
    Lists all permanently failed jobs from the failed_jobs table in a
    tabular format showing ID, queue, original job ID, failure time,
    and truncated error message. Already-retried jobs are marked.

  Examples:
    php artisan queue:failed                List all failed jobs
    php artisan queue:failed --queue=email  List failed email jobs only
    php artisan queue:failed --queue=risk   List failed risk calculation jobs only
HELP;
    }

    /**
     * Execute the command.
     *
     * @param array $args CLI arguments.
     *
     * @return int Exit code (0 = success).
     */
    public function handle(array $args): int
    {
        $options = $this->parseOptions($args);
        $queue = $this->option($options, 'queue');

        $query = FailedJob::orderBy('failed_at', 'DESC');

        if ($queue) :
            $query->where('queue', $queue);
        endif;

        $jobs = $query->get();

        if ($jobs->isEmpty()) :
            $this->success('No failed jobs found.');
            return 0;
        endif;

        $this->info("Failed Jobs (" . $jobs->count() . " total)");
        $this->line(str_repeat('-', 100));
        $this->line(sprintf(
            "  %-6s %-14s %-8s %-20s %-44s",
            'ID', 'Queue', 'Job ID', 'Failed At', 'Error'
        ));
        $this->line(str_repeat('-', 100));

        foreach ($jobs as $job) :
            $error = strlen($job->error_message) > 42
                ? substr($job->error_message, 0, 42) . '...'
                : $job->error_message;

            $retried = $job->retried_at ? ' (retried)' : '';

            $this->line(sprintf(
                "  %-6d %-14s %-8d %-20s %-44s",
                $job->id,
                $job->queue,
                $job->job_id,
                $job->failed_at . $retried,
                $error
            ));
        endforeach;

        $this->line(str_repeat('-', 100));

        return 0;
    }
}
