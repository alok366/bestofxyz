<?php

namespace Framework\Console\Commands;

use Framework\Console\BaseCommand;

class TestCommand extends BaseCommand
{
    /**
     * Get the command signature.
     *
     * @return string
     */
    public function signature(): string
    {
        return 'test';
    }

    /**
     * Get the command description.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Run PHPUnit tests [unit|integration|--filter=ClassName]';
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
    php artisan test [suite] [options]

  Options:
    suite               One of: unit, integration (runs that suite only)
    --filter=<pattern>  Filter by test class name or method name

  Description:
    Runs PHPUnit with optional suite selection or class/method filtering.
    Without arguments, runs all test suites.

  Examples:
    php artisan test                                Run all test suites
    php artisan test unit                           Run unit tests only
    php artisan test integration                    Run integration tests only
    php artisan test --filter=test_method_name      Run a single test method
HELP;
    }

    /**
     * Run PHPUnit with optional suite or filter arguments.
     *
     * Usage:
     *   php artisan test                        # all suites
     *   php artisan test unit                    # Unit Tests suite
     *   php artisan test integration             # Integration Tests suite
     *   php artisan test --filter=ClassName      # filter by class/method
     *
     * @param array $args CLI arguments.
     *
     * @return int Exit code from PHPUnit.
     */
    public function handle(array $args): int
    {
        $basePath = dirname(__DIR__, 3);
        $phpunit = $basePath . '/vendor/bin/phpunit';

        if (!file_exists($phpunit)) :
            $this->error("PHPUnit not found at $phpunit. Run: composer install");
            return 1;
        endif;

        $options = $this->parseOptions($args);
        $command = $phpunit;

        // Suite shorthand: php artisan test unit / php artisan test integration
        $suite = $args[0] ?? null;
        if ($suite && !str_starts_with($suite, '--')) :
            $suiteMap = [
                'unit'        => 'Unit Tests',
                'integration' => 'Integration Tests',
            ];

            $suiteName = $suiteMap[strtolower($suite)] ?? null;
            if (!$suiteName) :
                $this->error("Unknown suite: $suite. Available: unit, integration");
                return 1;
            endif;
            $command .= " --testsuite \"$suiteName\"";
        endif;

        // Filter: php artisan test --filter=ClassName
        $filter = $this->option($options, 'filter');
        if ($filter) :
            $command .= " --filter " . escapeshellarg($filter);
        endif;

        $this->info("Running: $command");
        $this->line("");

        passthru($command, $exitCode);

        return $exitCode;
    }
}
