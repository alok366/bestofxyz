<?php
namespace Guidelines\App\Sniffs\Architecture;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class NoRepositoriesInControllersSniff implements Sniff
{
    public function register()
    {
        return [T_USE, T_NEW];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $filePath = $phpcsFile->getFilename();
        if (strpos($filePath, '/Controllers/') === false) {
            return;
        }

        $tokens = $phpcsFile->getTokens();

        // Check for "use ...Repository" in use statements
        if ($tokens[$stackPtr]['code'] === T_USE) {
            $useStatement = '';
            $i = $stackPtr + 1;
            while (isset($tokens[$i]) && $tokens[$i]['code'] !== T_SEMICOLON) {
                $useStatement .= $tokens[$i]['content'];
                $i++;
            }
            if (preg_match('/Repositories(\\\\|;)/', $useStatement)) {
                $phpcsFile->addError(
                    'Controllers must not import Repositories directly. Use Services instead.',
                    $stackPtr,
                    'NoRepositoriesUse'
                );
            }
        }

        // Check for "new ...Repository" in code
        if ($tokens[$stackPtr]['code'] === T_NEW) {
            $i = $stackPtr + 1;
            while (isset($tokens[$i]) && $tokens[$i]['code'] === T_WHITESPACE) {
                $i++;
            }
            if (isset($tokens[$i]) && $tokens[$i]['code'] === T_STRING) {
                $className = $tokens[$i]['content'];
                if (preg_match('/Repositories$/', $className)) {
                    $phpcsFile->addError(
                        'Controllers must not instantiate Repositories directly. Use Services instead.',
                        $stackPtr,
                        'NoRepositoriesNew'
                    );
                }
            }
        }
    }
}