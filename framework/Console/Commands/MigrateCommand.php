<?php

namespace Framework\Console\Commands;

use Framework\Console\BaseCommand;
use Illuminate\Database\Capsule\Manager as DB;

class MigrateCommand extends BaseCommand
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
        return 'migrate';
    }

    /**
     * Get the command description.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Run pending database migrations';
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
    php artisan migrate

  Description:
    Scans the migrations directory for .php files not yet recorded in
    the _system_updates table. Lists pending migrations with file sizes,
    asks for confirmation, then executes each in order.

  Examples:
    php artisan migrate                     Run all pending migrations
    php artisan migrate 2>&1 | tee log.txt  Run migrations and save output to log
HELP;
    }

    /**
     * Execute the migrate command.
     *
     * @param array $args CLI arguments.
     *
     * @return int Exit code.
     */
    public function handle(array $args): int
    {
        $this->ensureTableExists();

        $files = glob($this->migrationsPath . "*.php");
        $pending = [];
        $count = 1;

        if (!$files) :
            $this->warn("No migration files found.");
            return 0;
        endif;

        foreach ($files as $file) :
            if ($this->isExecuted($file)) :
                continue;
            endif;
            $pending[] = $file;
        endforeach;

        $this->line("");

        if (empty($pending)) :
            $this->warn("Your database is up to date.");
            return 0;
        endif;

        $this->info("There are " . count($pending) . " pending migrations");
        $this->line("");

        foreach ($pending as $file) :
            $this->line("$count - " . $this->displayName($file) . " size " . filesize($file));
            $count++;
        endforeach;

        if (!$this->confirm("\nRun these migrations?")) :
            $this->info("Cancelled by user.");
            return 0;
        endif;

        $db = DB::connection();
        $count = 1;

        foreach ($pending as $file) :
            require($file);
            /** @var string $sql Defined inside the migration file */
            if ($db->unprepared($sql)) :
                $this->markAsExecuted($file);
                $this->success("$count - Success " . $this->displayName($file));
            else :
                $this->error("$count - Failed " . $this->displayName($file));
            endif;
            $count++;
        endforeach;

        return 0;
    }

    /**
     * Check if a migration has already been executed.
     *
     * @param string $file Full path to migration file.
     *
     * @return bool
     */
    private function isExecuted(string $file): bool
    {
        $record = DB::table('_system_updates')
            ->where('update_name', $this->displayName($file))
            ->first();

        return (bool) ($record->id ?? false);
    }

    /**
     * Record a migration as executed.
     *
     * @param string $file Full path to migration file.
     *
     * @return void
     */
    private function markAsExecuted(string $file): void
    {
        DB::table('_system_updates')->insert([
            'update_name' => $this->displayName($file),
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Ensure the _system_updates tracking table exists.
     *
     * @return void
     */
    private function ensureTableExists(): void
    {
        DB::statement("
            CREATE TABLE IF NOT EXISTS _system_updates (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                update_name VARCHAR(255) NOT NULL UNIQUE,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    /**
     * Get the display name (filename without path prefix).
     *
     * @param string $file Full path.
     *
     * @return string
     */
    private function displayName(string $file): string
    {
        return str_replace($this->migrationsPath, '', $file);
    }
}

