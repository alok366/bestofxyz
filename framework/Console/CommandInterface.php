<?php

namespace Framework\Console;

interface CommandInterface
{
    /**
     * Get the command signature (e.g., "migrate").
     *
     * @return string
     */
    public function signature(): string;

    /**
     * Get the command description for help output.
     *
     * @return string
     */
    public function description(): string;

    /**
     * Execute the command.
     *
     * @param array $args CLI arguments (from $argv, excluding script name and command name).
     *
     * @return int Exit code (0 = success, 1 = failure).
     */
    public function handle(array $args): int;

    /**
     * Get detailed help text for this command.
     *
     * Displayed when the user runs: php artisan <command> --help
     * Should include usage, options, and examples.
     *
     * @return string
     */
    public function help(): string;
}
