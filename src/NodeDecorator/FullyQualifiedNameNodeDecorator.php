<?php

declare (strict_types=1);
namespace TomasVotruba\ClassLeak\NodeDecorator;

use ClassLeak202411\PhpParser\Node\Stmt;
use ClassLeak202411\PhpParser\NodeTraverser;
use ClassLeak202411\PhpParser\NodeVisitor\NameResolver;
use ClassLeak202411\PhpParser\NodeVisitor\NodeConnectingVisitor;
final class FullyQualifiedNameNodeDecorator
{
    /**
     * @param Stmt[] $stmts
     */
    public function decorate(array $stmts) : void
    {
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new NameResolver());
        $nodeTraverser->addVisitor(new NodeConnectingVisitor());
        $nodeTraverser->traverse($stmts);
    }
}
