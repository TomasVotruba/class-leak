<?php

declare (strict_types=1);
namespace ClassLeak202501\PhpParser\Node\Stmt;

require __DIR__ . '/../StaticVar.php';
if (\false) {
    // For classmap-authoritative support.
    class StaticVar extends \ClassLeak202501\PhpParser\Node\StaticVar
    {
    }
}
