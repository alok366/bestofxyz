<?php

namespace Framework\Console\Commands;

use Framework\Console\BaseCommand;

class ChecksCommand extends BaseCommand
{
    /**
     * Get the command signature.
     *
     * @return string
     */
    public function signature(): string
    {
        return 'checks';
    }

    /**
     * Get the command description.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Run PHPCS code style checks [app|psr|psr:fix]';
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
    php artisan checks [scope] [options]

  Arguments:
    scope         One of: app, psr, psr:fix (default: both app + psr)
                  app      Run App-specific custom sniffs only
                  psr      Run PSR-12 standard checks only
                  psr:fix  Auto-fix PSR-12 violations via phpcbf

  Options:
    --file=path   Check a single file instead of all directories

  Description:
    Runs PHPCS code style checks against system/App/.
    Without a scope argument, runs both App and PSR-12 rulesets sequentially.
 
  Examples:
    php artisan checks                      Run all code style checks (App + PSR)
    php artisan checks app                  Run App custom sniffs only
    php artisan checks psr:fix              Auto-fix PSR-12 violations
HELP;
    }

    /**
     * Run PHPCS checks with optional scope and file target.
     *
     * Usage:
     *   php artisan checks                          # Both App + PSR rules
     *   php artisan checks app                      # App rules only
     *   php artisan checks psr                      # PSR-12 rules only
     *   php artisan checks psr:fix                  # Auto-fix PSR violations
     *   php artisan checks app --file=path/to/File.php  # Single file check
     *
     * @param array $args CLI arguments.
     *
     * @return int Exit code from PHPCS.
     */
    public function handle(array $args): int
    {
        $basePath = dirname(__DIR__, 3);
        $phpcs = $basePath . '/vendor/bin/phpcs';
        $phpcbf = $basePath . '/vendor/bin/phpcbf';
        $appRuleset = $basePath . '/guidelines/App/ruleset.xml';
        $psrRuleset = $basePath . '/guidelines/PSR/ruleset.xml';

        $options = $this->parseOptions($args);
        $file = $this->option($options, 'file');
        if ($file) {
            $targets = $file;
        } else {
            $defaultDirs = ['system/App/'];
            $existingDirs = array_filter($defaultDirs, fn(string $dir): bool => is_dir($basePath . '/' . $dir));
            $targets = implode(' ', $existingDirs);
        }

        // First non-option arg is the scope
        $scope = null;
        foreach ($args as $arg) :
            if (!str_starts_with($arg, '-')) :
                $scope = $arg;
                break;
            endif;
        endforeach;

        $commands = match ($scope) {
            'app'     => ["$phpcs --standard=$appRuleset $targets"],
            'psr'     => ["$phpcs --standard=$psrRuleset $targets"],
            'psr:fix' => ["$phpcbf --standard=$psrRuleset $targets"],
            default   => [
                "$phpcs --standard=$appRuleset $targets",
                "$phpcs --standard=$psrRuleset $targets",
            ],
        };

        $finalExitCode = 0;

        foreach ($commands as $command) :
            $this->info("Running: $command");
            $this->line("");
            passthru($command, $exitCode);

            if ($exitCode > $finalExitCode) :
                $finalExitCode = $exitCode;
            endif;
        endforeach;

        return $finalExitCode;
    }
}
