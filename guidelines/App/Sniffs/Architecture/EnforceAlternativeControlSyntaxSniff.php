<?php

namespace Guidelines\App\Sniffs\Architecture;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class EnforceAlternativeControlSyntaxSniff implements Sniff
{
    private array $targets = [
        T_IF,
        T_ELSEIF,
        T_ELSE,
        T_FOR,
        T_FOREACH,
        T_WHILE,
        T_SWITCH,
    ];

    public function register(): array
    {
        return $this->targets;
    }

    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        // Skip ELSE without condition (will be handled when its opening brace appears)
        $code = $tokens[$stackPtr]['code'];

        // Find end of condition for structures with parentheses
        $nextPtr = $stackPtr + 1;
        while (isset($tokens[$nextPtr]) && $tokens[$nextPtr]['code'] === T_WHITESPACE) {
            $nextPtr++;
        }

        // Detect if alternative syntax colon already used
        $scopeOpener = $tokens[$stackPtr]['scope_opener'] ?? null;
        $scopeCloser = $tokens[$stackPtr]['scope_closer'] ?? null;

        // If no scope opener (e.g. single-line without braces) skip
        if ($scopeOpener === null || $scopeCloser === null) {
            return;
        }

        // If opener is a colon token -> OK
        if ($tokens[$scopeOpener]['code'] === T_COLON) {
            return;
        }

        // If opener is a curly brace, enforce change
        if ($tokens[$scopeOpener]['code'] === T_OPEN_CURLY_BRACKET) {
            $phpcsFile->addError(
                'Use alternative control structure syntax (:) and matching end*; instead of braces.',
                $stackPtr,
                'DisallowBracesControlStructure'
            );
        }
    }
}