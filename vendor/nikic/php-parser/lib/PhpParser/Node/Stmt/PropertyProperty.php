<?php

declare (strict_types=1);
namespace ClassLeak202501\PhpParser\Node\Stmt;

use ClassLeak202501\PhpParser\Node\PropertyItem;
require __DIR__ . '/../PropertyItem.php';
if (\false) {
    // For classmap-authoritative support.
    class PropertyProperty extends PropertyItem
    {
    }
}
