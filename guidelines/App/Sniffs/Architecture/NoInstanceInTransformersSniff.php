<?php

namespace Guidelines\App\Sniffs\Architecture;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class NoInstanceInTransformersSniff implements Sniff
{
    public function register()
    {
        return [T_CLASS];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Get class name
        $classPtr = $stackPtr;
        $className = '';
        for ($i = $classPtr; $i < count($tokens); $i++) {
            if ($tokens[$i]['code'] === T_STRING) {
                $className = $tokens[$i]['content'];
                break;
            }
        }

        // Only apply to classes ending with 'Transformer'
        if (substr($className, -11) !== 'Transformer') {
            return;
        }

        // Find all function definitions in this class
        $classScope = $tokens[$classPtr]['scope_closer'] ?? null;
        $i = $classPtr;
        while ($i < ($classScope ?? count($tokens))) {
            if ($tokens[$i]['code'] === T_FUNCTION) {
                // Check if function is static
                $isStatic = false;
                for ($j = $i - 1; $j > $classPtr; $j--) {
                    if ($tokens[$j]['code'] === T_STATIC) {
                        $isStatic = true;
                        break;
                    }
                    // Stop if we hit another function or class
                    if (in_array($tokens[$j]['code'], [T_FUNCTION, T_CLASS])) {
                        break;
                    }
                }
                if (!$isStatic) {
                    $funcName = $tokens[$i+2]['content'] ?? '(unknown)';
                    $phpcsFile->addError(
                        "Non-static method '$funcName' found in Transformer class '$className'. Only static methods are allowed.",
                        $i,
                        'NonStaticMethodInTransformer'
                    );
                }
            }
            $i++;
        }
    }
}