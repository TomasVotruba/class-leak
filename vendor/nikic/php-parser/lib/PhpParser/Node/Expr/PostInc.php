<?php

declare (strict_types=1);
namespace ClassLeak202412\PhpParser\Node\Expr;

use ClassLeak202412\PhpParser\Node\Expr;
class PostInc extends Expr
{
    /** @var Expr Variable */
    public $var;
    /**
     * Constructs a post increment node.
     *
     * @param Expr $var Variable
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(Expr $var, array $attributes = [])
    {
        $this->attributes = $attributes;
        $this->var = $var;
    }
    public function getSubNodeNames() : array
    {
        return ['var'];
    }
    public function getType() : string
    {
        return 'Expr_PostInc';
    }
}
