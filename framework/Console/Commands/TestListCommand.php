<?php

namespace Framework\Console\Commands;

use Framework\Console\BaseCommand;

class TestListCommand extends BaseCommand
{
    /**
     * Get the command signature.
     *
     * @return string
     */
    public function signature(): string
    {
        return 'test:list';
    }

    /**
     * Get the command description.
     *
     * @return string
     */
    public function description(): string
    {
        return 'List all test classes and methods [unit|integration]';
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
    php artisan test:list [suite]

  Options:
    suite         One of: unit, integration (default: both suites)

  Description:
    Scans the tests/ directory and lists all test classes with their
    test methods, grouped by suite and folder. Shows total counts at
    the end along with quick-reference run commands.

  Examples:
    php artisan test:list                   List all test classes and methods
    php artisan test:list unit              List unit tests only
    php artisan test:list integration       List integration tests only
HELP;
    }

    /**
     * List test classes and their test methods, grouped by suite and folder.
     *
     * @param array $args First argument is optional suite filter (unit|integration).
     *
     * @return int Exit code.
     */
    public function handle(array $args): int
    {
        $basePath = dirname(__DIR__, 3) . '/tests';
        $suites = [
            'unit'        => $basePath . '/Unit',
            'integration' => $basePath . '/Integration',
        ];

        $suiteFilter = $args[0] ?? null;

        if ($suiteFilter !== null) :
            $suiteFilter = strtolower($suiteFilter);
            if (!isset($suites[$suiteFilter])) :
                $this->error("Unknown suite: $suiteFilter. Available: unit, integration");
                return 1;
            endif;
            $suites = [$suiteFilter => $suites[$suiteFilter]];
        endif;

        $totalClasses = 0;
        $totalMethods = 0;

        foreach ($suites as $suiteName => $suitePath) :
            if (!is_dir($suitePath)) :
                continue;
            endif;

            $this->line("");
            $this->info(strtoupper($suiteName) . ' TESTS');
            $this->info(str_repeat("\xe2\x94\x80", 60));

            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($suitePath, \RecursiveDirectoryIterator::SKIP_DOTS)
            );

            $grouped = [];
            foreach ($files as $file) :
                if ($file->getExtension() !== 'php') :
                    continue;
                endif;
                $relativePath = str_replace($suitePath . '/', '', $file->getPathname());
                $folder = dirname($relativePath);
                if ($folder === '.') :
                    $folder = '(root)';
                endif;
                $grouped[$folder][] = $file->getPathname();
            endforeach;

            ksort($grouped);

            foreach ($grouped as $folder => $testFiles) :
                $this->line("");
                $this->warn("  $folder/");

                sort($testFiles);
                foreach ($testFiles as $testFile) :
                    $className = basename($testFile, '.php');

                    if (strpos($className, 'Test') === false) :
                        continue;
                    endif;

                    $content = file_get_contents($testFile);
                    preg_match_all('/public\s+function\s+(test_\w+)\s*\(/', $content, $matches);
                    $methods = $matches[1] ?? [];

                    $totalClasses++;
                    $totalMethods += count($methods);

                    $this->success("    $className (" . count($methods) . " tests)");

                    foreach ($methods as $method) :
                        $label = str_replace('test_', '', $method);
                        $label = str_replace('_', ' ', $label);
                        $this->line("      \xe2\x94\x9c\xe2\x94\x80 $label");
                    endforeach;
                endforeach;
            endforeach;
        endforeach;

        $this->line("");
        $this->info(str_repeat("\xe2\x94\x80", 60));
        $this->success("Total: $totalClasses test classes, $totalMethods test methods");

        $this->line("");
        $this->warn("Run commands:");
        $this->line("  npm test                                  # all suites");
        $this->line("  npm run test:unit                         # unit tests only");
        $this->line("  npm run test:integration                  # integration tests only");
        $this->line("  npm run test:filter -- ClassName          # filter by class");
        $this->line("  npm run test:filter -- test_method_name   # filter by method");

        return 0;
    }
}
