<?php

namespace Guidelines\App\Sniffs\Architecture;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

/**
 * Flags `new ClassName()` inside __construct() of Controllers, Services, and Repositories.
 *
 * Enforces dependency injection: dependencies should be received via constructor
 * typehints, not instantiated with `new`. The Illuminate Container resolves the
 * dependency tree automatically.
 *
 * Scoped to:
 *   - system/App/Controllers/ (except BaseController — has framework bootstrapping)
 *   - system/App/Services/
 *   - system/App/Repositories/Smart/
 *   - system/App/Repositories/Eloquent/
 *
 * Does NOT apply to:
 *   - Models, Transformers, Utilities, Http/Middleware, Events, Listeners
 *   - tests/ directory
 *   - BaseController (legitimately creates framework objects)
 */
class NoNewInConstructorsSniff implements Sniff
{
    public function register()
    {
        return [T_FUNCTION];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $filePath = $phpcsFile->getFilename();

        // Only apply to these directories
        $inScope = strpos($filePath, '/Controllers/') !== false
            || strpos($filePath, '/Services/') !== false
            || strpos($filePath, '/Repositories/') !== false;

        if (!$inScope) {
            return;
        }

        // Skip BaseController — has legitimate framework bootstrapping
        if (strpos($filePath, 'BaseController.php') !== false) {
            return;
        }

        // Skip BaseService — abstract, sets repo pattern
        if (strpos($filePath, 'BaseService.php') !== false) {
            return;
        }

        // Skip base repository classes
        if (strpos($filePath, 'BaseEloquentRepository.php') !== false
            || strpos($filePath, 'BaseSmartRepository.php') !== false
            || strpos($filePath, 'BaseSmartCacheRepository.php') !== false) {
            return;
        }

        $tokens = $phpcsFile->getTokens();

        // Only check __construct methods
        $namePtr = $phpcsFile->findNext(T_STRING, $stackPtr + 1, $stackPtr + 4);
        if ($namePtr === false || $tokens[$namePtr]['content'] !== '__construct') {
            return;
        }

        // Find the scope of this function
        if (!isset($tokens[$stackPtr]['scope_opener']) || !isset($tokens[$stackPtr]['scope_closer'])) {
            return;
        }

        $scopeOpener = $tokens[$stackPtr]['scope_opener'];
        $scopeCloser = $tokens[$stackPtr]['scope_closer'];

        // Scan for T_NEW inside the constructor body
        for ($i = $scopeOpener; $i < $scopeCloser; $i++) {
            if ($tokens[$i]['code'] === T_NEW) {
                // Get the class name being instantiated
                $classNamePtr = $phpcsFile->findNext([T_STRING, T_NS_SEPARATOR], $i + 1, $scopeCloser);
                $className = '';
                while ($classNamePtr !== false
                    && isset($tokens[$classNamePtr])
                    && in_array($tokens[$classNamePtr]['code'], [T_STRING, T_NS_SEPARATOR])) {
                    $className .= $tokens[$classNamePtr]['content'];
                    $classNamePtr++;
                }

                $phpcsFile->addError(
                    'Constructors must use dependency injection — do not use "new %s()". '
                    . 'Receive dependencies via constructor typehints instead.',
                    $i,
                    'NoNewInConstructor',
                    [$className]
                );
            }
        }
    }
}
