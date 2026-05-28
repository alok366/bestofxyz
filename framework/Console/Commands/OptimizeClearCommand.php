<?php

namespace Framework\Console\Commands;

use Framework\Console\BaseCommand;

class OptimizeClearCommand extends BaseCommand
{
    /**
     * Get the command signature.
     *
     * @return string
     */
    public function signature(): string
    {
        return 'optimize:clear';
    }

    /**
     * Get the command description.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Clear application cache, sessions, and JWT tokens';
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
    php artisan optimize:clear

  Description:
    Clears the file cache directory (storage/cache/), all Redis sessions,
    and JWT refresh tokens. Equivalent to running cache:sessions after
    purging file cache.

  Examples:
    php artisan optimize:clear              Clear all caches, sessions, and JWT tokens
    php artisan optimize:clear && deploy    Clear caches before deploying
HELP;
    }

    /**
     * Clear the application cache directories, sessions, and JWT tokens.
     *
     * @param array $args CLI arguments (unused).
     *
     * @return int Exit code.
     */
    public function handle(array $args): int
    {
        $cachePath = dirname(__DIR__, 3) . '/storage/cache/';
        $this->clearDirectory($cachePath);
        $this->success("File cache cleared.");

        $sessionCommand = new CacheSessionsCommand();
        $sessionCommand->handle([]);

        return 0;
    }

    /**
     * Recursively clear a directory's contents.
     *
     * @param string $dir Directory path.
     *
     * @return void
     */
    private function clearDirectory(string $dir): void
    {
        if (!is_dir($dir)) :
            return;
        endif;

        $dir = rtrim($dir, '/') . '/';
        $files = array_diff(scandir($dir), ['.', '..']);

        foreach ($files as $file) :
            $path = $dir . $file;
            if (is_dir($path)) :
                $this->clearDirectory($path);
                if (is_dir($path)) :
                    rmdir($path);
                endif;
            else :
                unlink($path);
            endif;
        endforeach;
    }
}
