<?php

declare (strict_types=1);
namespace ClassLeak202504\PhpParser\Node\Stmt;

require __DIR__ . '/../StaticVar.php';
if (\false) {
    // For classmap-authoritative support.
    class StaticVar extends \ClassLeak202504\PhpParser\Node\StaticVar
    {
    }
}
