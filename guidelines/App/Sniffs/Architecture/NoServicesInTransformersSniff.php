<?php
namespace Guidelines\App\Sniffs\Architecture;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class NoServicesInTransformersSniff implements Sniff
{
    public function register()
    {
        return [T_USE, T_NEW];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $filePath = $phpcsFile->getFilename();
        if (strpos($filePath, '/Transformers/') === false) {
            return;
        }

        $tokens = $phpcsFile->getTokens();

        // Check for "use ...Service"
        if ($tokens[$stackPtr]['code'] === T_USE) {
            $useStatement = '';
            $i = $stackPtr + 1;
            while (isset($tokens[$i]) && $tokens[$i]['code'] !== T_SEMICOLON) {
                $useStatement .= $tokens[$i]['content'];
                $i++;
            }
            if (preg_match('/Service/', $useStatement)) {
                $phpcsFile->addError(
                    'Transformers must not import Services directly.',
                    $stackPtr,
                    'NoServicesUse'
                );
            }
        }

        // Check for "new ...Service"
        if ($tokens[$stackPtr]['code'] === T_NEW) {
            $i = $stackPtr + 1;
            while (isset($tokens[$i]) && $tokens[$i]['code'] === T_WHITESPACE) {
                $i++;
            }
            if (isset($tokens[$i]) && $tokens[$i]['code'] === T_STRING) {
                $className = $tokens[$i]['content'];
                if (strpos($className, 'Service') !== false) {
                    $phpcsFile->addError(
                        'Transformers must not instantiate Services directly.',
                        $stackPtr,
                        'NoServicesNew'
                    );
                }
            }
        }
    }
}