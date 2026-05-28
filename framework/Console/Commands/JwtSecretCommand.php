<?php

namespace Framework\Console\Commands;

use Framework\Console\BaseCommand;

class JwtSecretCommand extends BaseCommand
{
    /**
     * Get the command signature.
     *
     * @return string
     */
    public function signature(): string
    {
        return 'jwt:secret';
    }

    /**
     * Get the command description.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Generate a JWT secret and write it to .env';
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
    php artisan jwt:secret [--show] [--force]

  Description:
    Generates a cryptographically secure 64-char hex secret (256 bits) and
    writes it to the project .env as JWT_SECRET. Refuses to overwrite an
    existing non-empty value unless --force is passed.

  Options:
    --show     Print the generated secret to stdout instead of writing .env
    --force    Overwrite an existing JWT_SECRET value

  Examples:
    php artisan jwt:secret              Write a new JWT_SECRET to .env
    php artisan jwt:secret --show       Print a secret without touching .env
    php artisan jwt:secret --force      Replace the existing JWT_SECRET
HELP;
    }

    /**
     * Generate and persist a JWT secret.
     *
     * @param array $args CLI arguments.
     *
     * @return int Exit code.
     */
    public function handle(array $args): int
    {
        $show = in_array('--show', $args, true);
        $force = in_array('--force', $args, true);

        $secret = bin2hex(random_bytes(32));

        if ($show) :
            $this->info($secret);
            return 0;
        endif;

        $envPath = dirname(__DIR__, 3) . '/.env';

        if (!file_exists($envPath)) :
            $this->error("Error: .env file not found at {$envPath}");
            return 1;
        endif;

        $contents = file_get_contents($envPath);
        $pattern = '/^JWT_SECRET=(.*)$/m';

        if (preg_match($pattern, $contents, $matches)) :
            $existing = trim($matches[1]);
            if ($existing !== '' && !$force) :
                $this->warn("JWT_SECRET already set in .env. Use --force to overwrite.");
                return 1;
            endif;
            $updated = preg_replace($pattern, "JWT_SECRET={$secret}", $contents);
        else :
            $updated = rtrim($contents, "\n") . "\nJWT_SECRET={$secret}\n";
        endif;

        if (file_put_contents($envPath, $updated) === false) :
            $this->error("Error: failed to write .env");
            return 1;
        endif;

        $this->success("JWT_SECRET generated and written to .env");
        return 0;
    }
}
