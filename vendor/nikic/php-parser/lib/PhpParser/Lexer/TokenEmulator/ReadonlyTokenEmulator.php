<?php

declare (strict_types=1);
namespace ClassLeak202410\PhpParser\Lexer\TokenEmulator;

use ClassLeak202410\PhpParser\PhpVersion;
final class ReadonlyTokenEmulator extends KeywordEmulator
{
    public function getPhpVersion() : string
    {
        return PhpVersion::fromComponents(8, 1);
    }
    public function getKeywordString() : string
    {
        return 'readonly';
    }
    public function getKeywordToken() : int
    {
        return \T_READONLY;
    }
    protected function isKeywordContext(array $tokens, int $pos) : bool
    {
        if (!parent::isKeywordContext($tokens, $pos)) {
            return \false;
        }
        // Support "function readonly("
        return !(isset($tokens[$pos + 1]) && ($tokens[$pos + 1]->text === '(' || $tokens[$pos + 1]->id === \T_WHITESPACE && isset($tokens[$pos + 2]) && $tokens[$pos + 2]->text === '('));
    }
}
