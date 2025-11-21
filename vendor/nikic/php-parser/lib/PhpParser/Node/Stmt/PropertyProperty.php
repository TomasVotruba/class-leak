<?php

declare (strict_types=1);
namespace ClassLeak202511\PhpParser\Node\Stmt;

use ClassLeak202511\PhpParser\Node\PropertyItem;
require __DIR__ . '/../PropertyItem.php';
if (\false) {
    /**
     * For classmap-authoritative support.
     *
     * @deprecated use \PhpParser\Node\PropertyItem instead.
     */
    class PropertyProperty extends PropertyItem
    {
    }
}
