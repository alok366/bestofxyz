<?php

namespace Framework\Console\Commands;

use Framework\Console\BaseCommand;

class DocsCommand extends BaseCommand
{
    /**
     * Get the command signature.
     *
     * @return string
     */
    public function signature(): string
    {
        return 'docs';
    }

    /**
     * Get the command description.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Generate PHP API documentation via phpDocumentor';
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
    php artisan docs

  Description:
    Runs phpDocumentor to generate PHP API documentation from docblocks.
    Output is written to docs/generated/php/. Requires phpDocumentor
    to be installed via Composer.

  Examples:
    php artisan docs                        Generate PHP API documentation
    php artisan docs && open docs/          Generate and open in browser
HELP;
    }

    /**
     * Run phpDocumentor to generate PHP API docs.
     *
     * @param array $args CLI arguments (unused).
     *
     * @return int Exit code from phpDocumentor.
     */
    public function handle(array $args): int
    {
        $basePath = dirname(__DIR__, 3);
        $phpdoc = $basePath . '/vendor/bin/phpdoc';

        if (!file_exists($phpdoc)) :
            $this->error("phpDocumentor not found at $phpdoc. Run: composer install");
            return 1;
        endif;

        $command = "$phpdoc run -d . -t docs/generated/php --ignore vendor --ignore storage";

        $this->info("Generating PHP documentation...");
        $this->line("");

        passthru($command, $exitCode);

        if ($exitCode === 0) :
            $this->success("Documentation generated successfully!");
            $this->line("");
            $appUrl = rtrim(config('app.url', 'http://localhost'), '/');
            $this->info("  PHP docs:       $appUrl/docs/generated/php/");
            $this->info("  JS docs:        $appUrl/docs/generated/js/");
            $this->info("  Confluence:     https://behindmethods.atlassian.net/wiki/spaces/PKZ/pages/");
            $this->line("");
        endif;

        return $exitCode;
    }
}
