<?php

declare (strict_types=1);
namespace ClassLeak202410\PhpParser\Lexer\TokenEmulator;

use ClassLeak202410\PhpParser\Token;
abstract class KeywordEmulator extends TokenEmulator
{
    public abstract function getKeywordString() : string;
    public abstract function getKeywordToken() : int;
    public function isEmulationNeeded(string $code) : bool
    {
        return \strpos(\strtolower($code), $this->getKeywordString()) !== \false;
    }
    /** @param Token[] $tokens */
    protected function isKeywordContext(array $tokens, int $pos) : bool
    {
        $prevToken = $this->getPreviousNonSpaceToken($tokens, $pos);
        if ($prevToken === null) {
            return \false;
        }
        return (\is_array($prevToken) ? $prevToken[0] : $prevToken) !== \T_OBJECT_OPERATOR && (\is_array($prevToken) ? $prevToken[0] : $prevToken) !== \T_NULLSAFE_OBJECT_OPERATOR;
    }
    public function emulate(string $code, array $tokens) : array
    {
        $keywordString = $this->getKeywordString();
        foreach ($tokens as $i => $token) {
            if ($token->id === \T_STRING && \strtolower($token->text) === $keywordString && $this->isKeywordContext($tokens, $i)) {
                $token->id = $this->getKeywordToken();
            }
        }
        return $tokens;
    }
    /** @param Token[] $tokens */
    private function getPreviousNonSpaceToken(array $tokens, int $start) : ?Token
    {
        for ($i = $start - 1; $i >= 0; --$i) {
            if ((\is_array($tokens[$i]) ? $tokens[$i][0] : $tokens[$i]) === \T_WHITESPACE) {
                continue;
            }
            return $tokens[$i];
        }
        return null;
    }
    public function reverseEmulate(string $code, array $tokens) : array
    {
        $keywordToken = $this->getKeywordToken();
        foreach ($tokens as $token) {
            if ($token->id === $keywordToken) {
                $token->id = \T_STRING;
            }
        }
        return $tokens;
    }
}
