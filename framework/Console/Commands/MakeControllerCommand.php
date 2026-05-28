<?php

namespace Framework\Console\Commands;

use Framework\Console\BaseCommand;

class MakeControllerCommand extends BaseCommand
{
    /**
     * Get the command signature.
     *
     * @return string
     */
    public function signature(): string
    {
        return 'make:controller';
    }

    /**
     * Get the command description.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Create a new controller';
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
    php artisan make:controller <Name>

  Arguments:
    Name          The entity name in PascalCase (e.g. CouponReward)
                  "Controller" suffix is added automatically.

  Description:
    Creates a new controller in system/App/Controllers/ with the
    matching service injected via constructor.

  Examples:
    php artisan make:controller CouponReward     Creates CouponRewardController.php
HELP;
    }

    /**
     * Create a new controller file.
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
            $this->error('  Name argument is required. Usage: php artisan make:controller <Name>');
            return 1;
        endif;

        $className = $name . 'Controller';
        $filePath = __DIR__ . '/../../../system/App/Controllers/' . $className . '.php';

        if (file_exists($filePath)) :
            $this->error("  Controller already exists: system/App/Controllers/{$className}.php");
            return 1;
        endif;

        $content = $this->buildStub($name);

        file_put_contents($filePath, $content);
        $this->success("  Created: system/App/Controllers/{$className}.php");

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
     * Build the controller file content.
     *
     * @param string $name Entity name (without Controller suffix).
     *
     * @return string
     */
    private function buildStub(string $name): string
    {
        $serviceName = $name . 'Service';
        $serviceVar = lcfirst($name) . 'Service';
        $transformerName = $name . 'Transformer';

        return <<<PHP
<?php

namespace App\Controllers;

use App\Services\\{$serviceName};
use App\Transformers\\{$transformerName};

class {$name}Controller extends BaseController
{
    /**
     * Create a new {$name}Controller instance.
     *
     * @param {$serviceName} \${$serviceVar} {$name} service.
     */
    public function __construct(
        protected {$serviceName} \${$serviceVar}
    ) {
        parent::__construct();
    }

    /**
     * List all {$name} resources.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        \$requestData = \$this->request()->all();
        \$resources = \$this->{$serviceVar}->all(\$requestData);

        return \$this->response->json(
            collect(\$resources['data'])->map(fn(\$item) => {$transformerName}::toResponse(\$item))->toArray()
        );
    }

    /**
     * Show a single {$name} resource.
     *
     * @param string \$id Public ID.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string \$id)
    {
        \$resource = \$this->{$serviceVar}->find(\$id);

        if (!\$resource) :
            return \$this->response->json(['type' => 'not-found', 'title' => '{$name} not found.'], 404);
        endif;

        return \$this->response->json({$transformerName}::toResponse(\$resource));
    }

    /**
     * Store a new {$name} resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store()
    {
        \$requestData = \$this->request()->all();
        \$data = {$transformerName}::toDatabaseSchema(\$requestData);
        \$resource = \$this->{$serviceVar}->store(\$data);

        return \$this->response->json({$transformerName}::toResponse(\$resource), 201);
    }

    /**
     * Update an existing {$name} resource.
     *
     * @param string \$id Public ID.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(string \$id)
    {
        \$requestData = \$this->request()->all();
        \$data = {$transformerName}::toDatabaseSchema(\$requestData);
        \$this->{$serviceVar}->update(\$id, \$data);

        return \$this->response->json(null, 204);
    }

    /**
     * Delete a {$name} resource.
     *
     * @param string \$id Public ID.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string \$id)
    {
        \$this->{$serviceVar}->delete(\$id);

        return \$this->response->json(null, 204);
    }
}
PHP;
    }
}
