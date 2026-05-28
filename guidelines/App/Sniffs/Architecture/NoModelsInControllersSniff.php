<?php
namespace Guidelines\App\Sniffs\Architecture;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class NoModelsInControllersSniff implements Sniff
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

        // Check for "use Models\..." in use statements
        if ($tokens[$stackPtr]['code'] === T_USE) {
            $useStatement = '';
            $i = $stackPtr + 1;
            while (isset($tokens[$i]) && $tokens[$i]['code'] !== T_SEMICOLON) {
                $useStatement .= $tokens[$i]['content'];
                $i++;
            }
            if (preg_match('/Models\\\\/', $useStatement)) {
                $phpcsFile->addError(
                    'Controllers must not import Models directly. Use Services instead.',
                    $stackPtr,
                    'NoModelsUse'
                );
            }
        }

        // Check for "new Models\..." in code
        if ($tokens[$stackPtr]['code'] === T_NEW) {
            $i = $stackPtr + 1;
            while (isset($tokens[$i]) && $tokens[$i]['code'] === T_WHITESPACE) {
                $i++;
            }
            if (isset($tokens[$i]) && $tokens[$i]['code'] === T_STRING) {
                $className = $tokens[$i]['content'];
                if (strpos($className, 'Models\\') === 0) {
                    $phpcsFile->addError(
                        'Controllers must not instantiate Models directly. Use Services instead.',
                        $stackPtr,
                        'NoModelsNew'
                    );
                }
            }
        }
    }
}
