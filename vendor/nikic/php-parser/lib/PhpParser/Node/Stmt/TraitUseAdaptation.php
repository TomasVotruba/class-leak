<?php

declare (strict_types=1);
namespace ClassLeak202410\PhpParser\Node\Stmt;

use ClassLeak202410\PhpParser\Node;
abstract class TraitUseAdaptation extends Node\Stmt
{
    /** @var Node\Name|null Trait name */
    public $trait;
    /** @var Node\Identifier Method name */
    public $method;
}
