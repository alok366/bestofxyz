<?php

namespace Framework\Console\Commands;

use Framework\Console\BaseCommand;

class MakeRepositoryCommand extends BaseCommand
{
    /**
     * Get the command signature.
     *
     * @return string
     */
    public function signature(): string
    {
        return 'make:repository';
    }

    /**
     * Get the command description.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Create a Smart + Eloquent repository pair';
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
    php artisan make:repository <Name>

  Arguments:
    Name          The entity name in PascalCase (e.g. CouponReward)
                  Smart/Eloquent prefixes and "Repository" suffix added automatically.

  Description:
    Creates both repositories:
      - system/App/Repositories/Smart/Smart<Name>Repository.php
      - system/App/Repositories/Eloquent/Eloquent<Name>Repository.php

    The EloquentRepository is wired to the matching Model.
    The SmartRepository delegates to the EloquentRepository.

  Examples:
    php artisan make:repository CouponReward
HELP;
    }

    /**
     * Create Smart and Eloquent repository files.
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
            $this->error('  Name argument is required. Usage: php artisan make:repository <Name>');
            return 1;
        endif;

        $created = 0;

        $eloquentResult = $this->createEloquent($name);
        if ($eloquentResult === 0) :
            $created++;
        elseif ($eloquentResult === 2) :
            // already exists, continue
        else :
            return 1;
        endif;

        $smartResult = $this->createSmart($name);
        if ($smartResult === 0) :
            $created++;
        elseif ($smartResult === 2) :
            // already exists, continue
        else :
            return 1;
        endif;

        if ($created === 0) :
            $this->warn('  Both repositories already exist.');
        endif;

        return 0;
    }

    /**
     * Create the Eloquent repository file.
     *
     * @param string $name Entity name.
     *
     * @return int 0 = created, 2 = exists.
     */
    private function createEloquent(string $name): int
    {
        $className = 'Eloquent' . $name . 'Repository';
        $filePath = __DIR__ . '/../../../system/App/Repositories/Eloquent/' . $className . '.php';

        if (file_exists($filePath)) :
            $this->warn("  Already exists: system/App/Repositories/Eloquent/{$className}.php");
            return 2;
        endif;

        $content = $this->buildEloquentStub($name);
        file_put_contents($filePath, $content);
        $this->success("  Created: system/App/Repositories/Eloquent/{$className}.php");

        return 0;
    }

    /**
     * Create the Smart repository file.
     *
     * @param string $name Entity name.
     *
     * @return int 0 = created, 2 = exists.
     */
    private function createSmart(string $name): int
    {
        $className = 'Smart' . $name . 'Repository';
        $filePath = __DIR__ . '/../../../system/App/Repositories/Smart/' . $className . '.php';

        if (file_exists($filePath)) :
            $this->warn("  Already exists: system/App/Repositories/Smart/{$className}.php");
            return 2;
        endif;

        $content = $this->buildSmartStub($name);
        file_put_contents($filePath, $content);
        $this->success("  Created: system/App/Repositories/Smart/{$className}.php");

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
     * Build the Eloquent repository file content.
     *
     * @param string $name Entity name.
     *
     * @return string
     */
    private function buildEloquentStub(string $name): string
    {
        return <<<PHP
<?php

namespace App\Repositories\Eloquent;

use App\Models\\{$name};

class Eloquent{$name}Repository extends BaseEloquentRepository
{
    /**
     * Create a new Eloquent{$name}Repository instance.
     *
     * @param {$name} \$model Eloquent model.
     *
     * @return void
     */
    public function __construct({$name} \$model)
    {
        \$this->model = \$model;
    }
}
PHP;
    }

    /**
     * Build the Smart repository file content.
     *
     * @param string $name Entity name.
     *
     * @return string
     */
    private function buildSmartStub(string $name): string
    {
        $eloquentClass = 'Eloquent' . $name . 'Repository';

        return <<<PHP
<?php

namespace App\Repositories\Smart;

use App\Repositories\Eloquent\\{$eloquentClass};

class Smart{$name}Repository extends BaseSmartRepository
{
    protected \$eloquent;

    /**
     * Create a new Smart{$name}Repository instance.
     *
     * @param {$eloquentClass} \$eloquent Eloquent implementation.
     *
     * @return void
     */
    public function __construct({$eloquentClass} \$eloquent)
    {
        \$this->eloquent = \$eloquent;
    }
}
PHP;
    }
}
