<?php

declare (strict_types=1);
namespace ClassLeak202504\PhpParser\Node\Stmt;

use ClassLeak202504\PhpParser\Node\DeclareItem;
require __DIR__ . '/../DeclareItem.php';
if (\false) {
    // For classmap-authoritative support.
    class DeclareDeclare extends DeclareItem
    {
    }
}
