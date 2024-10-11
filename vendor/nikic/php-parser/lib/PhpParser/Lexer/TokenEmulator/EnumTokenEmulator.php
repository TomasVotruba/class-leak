<?php

declare (strict_types=1);
namespace ClassLeak202410\PhpParser\Lexer\TokenEmulator;

use ClassLeak202410\PhpParser\PhpVersion;
final class EnumTokenEmulator extends KeywordEmulator
{
    public function getPhpVersion() : string
    {
        return PhpVersion::fromComponents(8, 1);
    }
    public function getKeywordString() : string
    {
        return 'enum';
    }
    public function getKeywordToken() : int
    {
        return \T_ENUM;
    }
    protected function isKeywordContext(array $tokens, int $pos) : bool
    {
        return parent::isKeywordContext($tokens, $pos) && isset($tokens[$pos + 2]) && $tokens[$pos + 1]->id === \T_WHITESPACE && $tokens[$pos + 2]->id === \T_STRING;
    }
}
