<?php

namespace Framework\Console\Commands;

use Framework\Console\BaseCommand;

class MakeTransformerCommand extends BaseCommand
{
    /**
     * Get the command signature.
     *
     * @return string
     */
    public function signature(): string
    {
        return 'make:transformer';
    }

    /**
     * Get the command description.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Create a new transformer';
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
    php artisan make:transformer <Name>

  Arguments:
    Name          The entity name in PascalCase (e.g. Resource)
                  "Transformer" suffix is added automatically.

  Description:
    Creates a new transformer in system/App/Transformers/ with static
    toResponse() and toDatabaseSchema() methods (bidirectional pattern).

  Examples:
    php artisan make:transformer Resource    Creates ResourceTransformer.php
HELP;
    }

    /**
     * Create a new transformer file.
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
            $this->error('  Name argument is required. Usage: php artisan make:transformer <Name>');
            return 1;
        endif;

        $className = $name . 'Transformer';
        $filePath = __DIR__ . '/../../../system/App/Transformers/' . $className . '.php';

        if (file_exists($filePath)) :
            $this->error("  Transformer already exists: system/App/Transformers/{$className}.php");
            return 1;
        endif;

        $content = $this->buildStub($name);

        file_put_contents($filePath, $content);
        $this->success("  Created: system/App/Transformers/{$className}.php");

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
     * Build the transformer file content.
     *
     * @param string $name Entity name (without Transformer suffix).
     *
     * @return string
     */
    private function buildStub(string $name): string
    {
        $varName = lcfirst($name);

        return <<<PHP
<?php

namespace App\Transformers;

class {$name}Transformer
{
    /**
     * Format a {$name} resource for API response.
     *
     * @param object \${$varName} The {$name} resource.
     *
     * @return array
     */
    public static function toResponse(object \${$varName}): array
    {
        return [
            'id' => \${$varName}->public_id,
        ];
    }

    /**
     * Shape request input into a database-ready array.
     *
     * @param array \$input Validated request data.
     *
     * @return array
     */
    public static function toDatabaseSchema(array \$input): array
    {
        return \$input;
    }
}
PHP;
    }
}
