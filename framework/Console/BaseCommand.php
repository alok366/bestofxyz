<?php

namespace Framework\Console;

abstract class BaseCommand implements CommandInterface
{
    /**
     * Default help output — shows signature and description.
     *
     * Override in subclasses for detailed usage, options, and examples.
     *
     * @return string
     */
    public function help(): string
    {
        return $this->description();
    }

    /**
     * Check if --help was passed, and print help if so.
     *
     * Call this at the start of handle() to auto-respond to --help.
     * Returns true if --help was shown (caller should return 0).
     *
     * @param array $args CLI arguments.
     * @return bool True if help was displayed.
     */
    protected function showHelpIfRequested(array $args): bool
    {
        if (in_array('--help', $args) || in_array('-h', $args)) :
            echo PHP_EOL;
            $this->info("  " . $this->signature());
            echo "  " . $this->description() . PHP_EOL . PHP_EOL;
            echo $this->help() . PHP_EOL;
            return true;
        endif;

        return false;
    }

    /**
     * Write a line to stdout.
     *
     * @param string $message
     *
     * @return void
     */
    protected function line(string $message): void
    {
        echo $message . PHP_EOL;
    }

    /**
     * Write a success message (green).
     *
     * @param string $message
     *
     * @return void
     */
    protected function success(string $message): void
    {
        echo "\033[32m$message\033[0m" . PHP_EOL;
    }

    /**
     * Write an error message (red).
     *
     * @param string $message
     *
     * @return void
     */
    protected function error(string $message): void
    {
        echo "\033[31m$message\033[0m" . PHP_EOL;
    }

    /**
     * Write a warning message (yellow).
     *
     * @param string $message
     *
     * @return void
     */
    protected function warn(string $message): void
    {
        echo "\033[33m$message\033[0m" . PHP_EOL;
    }

    /**
     * Write an info message (blue).
     *
     * @param string $message
     *
     * @return void
     */
    protected function info(string $message): void
    {
        echo "\033[34m$message\033[0m" . PHP_EOL;
    }

    /**
     * Ask for confirmation from stdin.
     *
     * @param string $question
     *
     * @return bool
     */
    protected function confirm(string $question): bool
    {
        $this->warn($question . " (Y/n): ");
        $handle = fopen("php://stdin", "r");
        $input = trim(fgets($handle));

        return strtolower($input) !== 'n';
    }

    /**
     * Parse named options from args (e.g., --id=123, --dry-run).
     *
     * @param array $args
     *
     * @return array Associative array of options.
     */
    protected function parseOptions(array $args): array
    {
        $options = [];
        foreach ($args as $arg) :
            if (str_starts_with($arg, '--')) :
                $arg = ltrim($arg, '-');
                if (str_contains($arg, '=')) :
                    [$key, $value] = explode('=', $arg, 2);
                    $options[$key] = $value;
                else :
                    $options[$arg] = true;
                endif;
            endif;
        endforeach;

        return $options;
    }

    /**
     * Get a specific option value, or default.
     *
     * @param array $options Parsed options from parseOptions().
     * @param string $key Option name.
     * @param mixed $default Default value if not present.
     *
     * @return mixed
     */
    protected function option(array $options, string $key, mixed $default = null): mixed
    {
        return $options[$key] ?? $default;
    }
}
