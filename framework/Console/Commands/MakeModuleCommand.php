<?php

namespace Framework\Console\Commands;

use Framework\Console\BaseCommand;

class MakeModuleCommand extends BaseCommand
{
    /**
     * Get the command signature.
     *
     * @return string
     */
    public function signature(): string
    {
        return 'make:module';
    }

    /**
     * Get the command description.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Scaffold all layers for a new module';
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
    php artisan make:module <Name> [options]

  Arguments:
    Name          The entity name in PascalCase (e.g. Resource)

  Options:
    --table=name  Specify the database table name for the model
    --skip=layers Comma-separated layers to skip (model,controller,service,repository,transformer)

  Description:
    Creates the full layer stack for a new module:
      - Model              system/App/Models/<Name>.php
      - EloquentRepository system/App/Repositories/Eloquent/Eloquent<Name>Repository.php
      - SmartRepository    system/App/Repositories/Smart/Smart<Name>Repository.php
      - Service            system/App/Services/<Name>Service.php
      - Transformer        system/App/Transformers/<Name>Transformer.php
      - Controller         system/App/Controllers/<Name>Controller.php

  Examples:
    php artisan make:module Resource
    php artisan make:module Resource --table=resources
    php artisan make:module Resource --skip=controller,transformer
HELP;
    }

    /**
     * Scaffold all layers for a new module.
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
            $this->error('  Name argument is required. Usage: php artisan make:module <Name>');
            return 1;
        endif;

        $skip = array_filter(
            array_map('trim', explode(',', $this->option($options, 'skip', '')))
        );

        $this->line('');
        $this->info("  Scaffolding module: {$name}");
        $this->line('');

        $failed = 0;

        // Order matters: Model → Eloquent Repo → Smart Repo → Service → Transformer → Controller
        if (!in_array('model', $skip)) :
            $tableArgs = [];
            $tableOption = $this->option($options, 'table');
            if ($tableOption) :
                $tableArgs[] = $name;
                $tableArgs[] = "--table={$tableOption}";
            else :
                $tableArgs[] = $name;
            endif;
            $failed += (new MakeModelCommand())->handle($tableArgs);
        endif;

        if (!in_array('repository', $skip)) :
            $failed += (new MakeRepositoryCommand())->handle([$name]);
        endif;

        if (!in_array('service', $skip)) :
            $failed += (new MakeServiceCommand())->handle([$name]);
        endif;

        if (!in_array('transformer', $skip)) :
            $failed += (new MakeTransformerCommand())->handle([$name]);
        endif;

        if (!in_array('controller', $skip)) :
            $failed += (new MakeControllerCommand())->handle([$name]);
        endif;

        $this->line('');
        if ($failed === 0) :
            $this->success('  Module scaffolded successfully.');
        else :
            $this->warn('  Module scaffolded with some skipped files (already exist).');
        endif;

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
}
