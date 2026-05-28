<?php

namespace Framework\Console;

class CommandRegistry
{
    /** @var array<string, CommandInterface> */
    private array $commands = [];

    /**
     * Register a command instance.
     *
     * @param CommandInterface $command
     *
     * @return void
     */
    public function register(CommandInterface $command): void
    {
        $this->commands[$command->signature()] = $command;
    }

    /**
     * Resolve and execute a command by name.
     *
     * @param string $name Command signature.
     * @param array $args CLI arguments.
     *
     * @return int Exit code.
     */
    public function run(string $name, array $args): int
    {
        if (!isset($this->commands[$name])) :
            echo "\033[33mUnknown command: $name\033[0m" . PHP_EOL;
            $this->listCommands();
            return 1;
        endif;

        // Intercept --help / -h for any command
        if (in_array('--help', $args) || in_array('-h', $args)) :
            $cmd = $this->commands[$name];
            echo PHP_EOL;
            echo "  \033[34m" . $cmd->signature() . "\033[0m" . PHP_EOL;
            echo "  " . $cmd->description() . PHP_EOL . PHP_EOL;
            echo $cmd->help() . PHP_EOL;
            return 0;
        endif;

        return $this->commands[$name]->handle($args);
    }

    /**
     * Print all registered commands with descriptions.
     *
     * @return void
     */
    public function listCommands(): void
    {
        echo PHP_EOL . "Available commands:" . PHP_EOL;

        $maxLen = 0;
        foreach ($this->commands as $sig => $cmd) :
            $maxLen = max($maxLen, strlen($sig));
        endforeach;

        $sorted = $this->commands;
        ksort($sorted);

        foreach ($sorted as $sig => $cmd) :
            $padding = str_repeat(' ', $maxLen - strlen($sig) + 4);
            echo "  \033[32m$sig\033[0m$padding{$cmd->description()}" . PHP_EOL;
        endforeach;

        echo PHP_EOL;
    }

    /**
     * Check if a command is registered.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->commands[$name]);
    }
}
