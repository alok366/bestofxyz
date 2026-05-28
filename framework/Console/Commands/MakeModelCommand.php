<?php

namespace Framework\Console\Commands;

use Framework\Console\BaseCommand;

class MakeModelCommand extends BaseCommand
{
    /**
     * Get the command signature.
     *
     * @return string
     */
    public function signature(): string
    {
        return 'make:model';
    }

    /**
     * Get the command description.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Create a new Eloquent model';
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
    php artisan make:model <Name> [options]

  Arguments:
    Name          The model name in PascalCase (e.g. CouponReward)

  Options:
    --table=name  Specify the database table name (default: snake_case plural)

  Description:
    Creates a new Eloquent model in system/App/Models/.

  Examples:
    php artisan make:model CouponReward
    php artisan make:model CouponReward --table=coupon_rewards
HELP;
    }

    /**
     * Create a new Eloquent model file.
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

        $options = $this->parseOptions($args);
        $name = $this->getNameArgument($args);

        if (!$name) :
            $this->error('  Name argument is required. Usage: php artisan make:model <Name>');
            return 1;
        endif;

        $filePath = __DIR__ . '/../../../system/App/Models/' . $name . '.php';

        if (file_exists($filePath)) :
            $this->error("  Model already exists: system/App/Models/{$name}.php");
            return 1;
        endif;

        $table = $this->option($options, 'table', $this->toTableName($name));
        $content = $this->buildStub($name, $table);

        file_put_contents($filePath, $content);
        $this->success("  Created: system/App/Models/{$name}.php");

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
     * Convert PascalCase name to snake_case plural table name.
     *
     * @param string $name PascalCase model name.
     *
     * @return string
     */
    private function toTableName(string $name): string
    {
        $snake = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $name));

        if (str_ends_with($snake, 'y') && !str_ends_with($snake, 'ey') && !str_ends_with($snake, 'ay') && !str_ends_with($snake, 'oy')) :
            return substr($snake, 0, -1) . 'ies';
        endif;

        if (str_ends_with($snake, 's') || str_ends_with($snake, 'sh') || str_ends_with($snake, 'ch') || str_ends_with($snake, 'x')) :
            return $snake . 'es';
        endif;

        return $snake . 's';
    }

    /**
     * Build the model file content from stub.
     *
     * @param string $name Model class name.
     * @param string $table Database table name.
     *
     * @return string
     */
    private function buildStub(string $name, string $table): string
    {
        return <<<PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class {$name} extends Model
{
    protected \$table = '{$table}';

    protected \$fillable = [
        'public_id',
    ];
}
PHP;
    }
}
