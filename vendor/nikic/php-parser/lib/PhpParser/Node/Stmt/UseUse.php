<?php

declare (strict_types=1);
namespace ClassLeak202501\PhpParser\Node\Stmt;

use ClassLeak202501\PhpParser\Node\UseItem;
require __DIR__ . '/../UseItem.php';
if (\false) {
    // For classmap-authoritative support.
    class UseUse extends UseItem
    {
    }
}
