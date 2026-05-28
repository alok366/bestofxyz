<?php
namespace Guidelines\App\Sniffs\Architecture;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class NoTransformersInServicesSniff implements Sniff
{
    public function register()
    {
        return [T_USE, T_NEW];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $filePath = $phpcsFile->getFilename();
        if (strpos($filePath, '/Services/') === false) {
            return;
        }

        $tokens = $phpcsFile->getTokens();

        // Check for "use ...Transformer"
        if ($tokens[$stackPtr]['code'] === T_USE) {
            $useStatement = '';
            $i = $stackPtr + 1;
            while (isset($tokens[$i]) && $tokens[$i]['code'] !== T_SEMICOLON) {
                $useStatement .= $tokens[$i]['content'];
                $i++;
            }
            if (preg_match('/Transformer/', $useStatement)) {
                $phpcsFile->addError(
                    'Services must not import Transformers directly.',
                    $stackPtr,
                    'NoTransformersUse'
                );
            }
        }

        // Check for "new ...Transformer"
        if ($tokens[$stackPtr]['code'] === T_NEW) {
            $i = $stackPtr + 1;
            while (isset($tokens[$i]) && $tokens[$i]['code'] === T_WHITESPACE) {
                $i++;
            }
            if (isset($tokens[$i]) && $tokens[$i]['code'] === T_STRING) {
                $className = $tokens[$i]['content'];
                if (strpos($className, 'Transformer') !== false) {
                    $phpcsFile->addError(
                        'Services must not instantiate Transformers directly.',
                        $stackPtr,
                        'NoTransformersNew'
                    );
                }
            }
        }
    }
}