<?php

declare (strict_types=1);
namespace ClassLeak202501\PhpParser\Node\Expr;

require __DIR__ . '/../ClosureUse.php';
if (\false) {
    // For classmap-authoritative support.
    class ClosureUse extends \ClassLeak202501\PhpParser\Node\ClosureUse
    {
    }
}
