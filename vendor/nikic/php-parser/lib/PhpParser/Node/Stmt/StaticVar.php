<?php

declare (strict_types=1);
namespace ClassLeak202605\PhpParser\Node\Stmt;

require __DIR__ . '/../StaticVar.php';
if (\false) {
    /**
     * For classmap-authoritative support.
     *
     * @deprecated use \PhpParser\Node\StaticVar instead.
     */
    class StaticVar extends \ClassLeak202605\PhpParser\Node\StaticVar
    {
    }
}
