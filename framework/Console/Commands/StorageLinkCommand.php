<?php

namespace Framework\Console\Commands;

use Framework\Console\BaseCommand;

class StorageLinkCommand extends BaseCommand
{
    /**
     * Get the command signature.
     *
     * @return string
     */
    public function signature(): string
    {
        return 'storage:link';
    }

    /**
     * Get the command description.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Create public/storage symlink to storage/';
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
    php artisan storage:link

  Description:
    Creates a symbolic link from public/storage to the storage/ directory,
    making stored files accessible via the web server. Skips if the symlink
    already exists; errors if a non-symlink file/directory is in the way.

  Examples:
    php artisan storage:link                Create the public/storage symlink
    php artisan storage:link && ls -la public/storage  Create and verify the link
HELP;
    }

    /**
     * Create the public/storage -> storage/ symlink.
     *
     * @param array $args CLI arguments (unused).
     *
     * @return int Exit code.
     */
    public function handle(array $args): int
    {
        $basePath = dirname(__DIR__, 3);
        $link = $basePath . '/public/storage';
        $target = $basePath . '/storage';

        if (file_exists($link)) :
            if (is_link($link)) :
                $this->warn("Symlink already exists: public/storage -> storage/");
                return 0;
            endif;
            $this->error("Error: public/storage exists but is not a symlink. Remove it manually first.");
            return 1;
        endif;

        if (!is_dir($target)) :
            $this->error("Error: storage/ directory not found.");
            return 1;
        endif;

        symlink($target, $link);
        $this->success("Symlink created: public/storage -> storage/");

        return 0;
    }
}
