<?php

namespace Framework\Console\Commands;

use Framework\Console\BaseCommand;

class MakeServiceCommand extends BaseCommand
{
    /**
     * Get the command signature.
     *
     * @return string
     */
    public function signature(): string
    {
        return 'make:service';
    }

    /**
     * Get the command description.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Create a new service';
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
    php artisan make:service <Name>

  Arguments:
    Name          The entity name in PascalCase (e.g. CouponReward)
                  "Service" suffix is added automatically.

  Description:
    Creates a new service in system/App/Services/ extending BaseService,
    wired to the matching SmartRepository.

  Examples:
    php artisan make:service CouponReward       Creates CouponRewardService.php
HELP;
    }

    /**
     * Create a new service file.
     *
     * @param array $args CLI arguments.
     *
     * @return int Exit code.
     */
    public function handle(array $args): int
    {
        if ($this->showHelpIfRequested($args)) :
            return 0;
        endif;

        $name = $this->getNameArgument($args);

        if (!$name) :
            $this->error('  Name argument is required. Usage: php artisan make:service <Name>');
            return 1;
        endif;

        $className = $name . 'Service';
        $filePath = __DIR__ . '/../../../system/App/Services/' . $className . '.php';

        if (file_exists($filePath)) :
            $this->error("  Service already exists: system/App/Services/{$className}.php");
            return 1;
        endif;

        $content = $this->buildStub($name);

        file_put_contents($filePath, $content);
        $this->success("  Created: system/App/Services/{$className}.php");

        return 0;
    }

    /**
     * Extract the name argument (first non-option arg).
     *
     * @param array $args CLI arguments.
     *
     * @return string|null
     */
    private function getNameArgument(array $args): ?string
    {
        foreach ($args as $arg) :
            if (!str_starts_with($arg, '-')) :
                return $arg;
            endif;
        endforeach;

        return null;
    }

    /**
     * Build the service file content.
     *
     * @param string $name Entity name (without Service suffix).
     *
     * @return string
     */
    private function buildStub(string $name): string
    {
        $repoName = 'Smart' . $name . 'Repository';

        return <<<PHP
<?php

namespace App\Services;

use App\Repositories\Smart\\{$repoName};

class {$name}Service extends BaseService
{
    /**
     * Create a new {$name}Service instance.
     *
     * @param {$repoName} \$repo Smart repository.
     *
     * @return void
     */
    public function __construct({$repoName} \$repo)
    {
        parent::__construct(\$repo, \$repo);
    }
}
PHP;
    }
}
