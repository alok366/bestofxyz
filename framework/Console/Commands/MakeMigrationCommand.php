<?php

namespace Framework\Console\Commands;

use Framework\Console\BaseCommand;

class MakeMigrationCommand extends BaseCommand
{
    private string $migrationsPath;

    /**
     * @param string $migrationsPath Path to database migrations directory.
     */
    public function __construct(string $migrationsPath)
    {
        $this->migrationsPath = $migrationsPath;
    }

    /**
     * Get the command signature.
     *
     * @return string
     */
    public function signature(): string
    {
        return 'make:migration';
    }

    /**
     * Get the command description.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Create a new migration file';
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
    php artisan make:migration [name]

  Options:
    name          Optional descriptive name appended to the timestamp

  Description:
    Creates a new migration file in database/migrations/ with a
    YYYY-MM-DD-HH-MM-SS timestamp prefix and a $sql placeholder.

  Examples:
    php artisan make:migration                              Create 2026-04-01-12-00-00.php
    php artisan make:migration alter_users_add_phone         Create timestamped ALTER TABLE migration
HELP;
    }

    /**
     * Create a new migration file with timestamp prefix.
     *
     * @param array $args CLI arguments. First argument is optional migration name.
     *
     * @return int Exit code.
     */
    public function handle(array $args): int
    {
        $timestamp = date('Y-m-d-H-i-s');
        $name = $args[0] ?? null;

        $fileName = $name
            ? "$timestamp-$name.php"
            : "$timestamp.php";

        $filePath = $this->migrationsPath . $fileName;

        $content = "<?php\n\n\$sql = \"SQL QUERY GOES HERE\";\n";
        file_put_contents($filePath, $content);

        $this->success("Migration created: $fileName");

        return 0;
    }
}
