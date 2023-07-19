<?php

declare (strict_types=1);
namespace TomasVotruba\ClassLeak\NodeVisitor;

use ClassLeak202307\PhpParser\Node;
use ClassLeak202307\PhpParser\Node\Expr\ConstFetch;
use ClassLeak202307\PhpParser\Node\Expr\FuncCall;
use ClassLeak202307\PhpParser\Node\Name;
use ClassLeak202307\PhpParser\Node\Stmt;
use ClassLeak202307\PhpParser\Node\Stmt\ClassMethod;
use ClassLeak202307\PhpParser\Node\Stmt\Namespace_;
use ClassLeak202307\PhpParser\NodeTraverser;
use ClassLeak202307\PhpParser\NodeVisitorAbstract;
final class UsedClassNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string[]
     */
    private $usedNames = [];
    /**
     * @param Stmt[] $nodes
     * @return Stmt[]
     */
    public function beforeTraverse(array $nodes) : array
    {
        $this->usedNames = [];
        return $nodes;
    }
    /**
     * @return \PhpParser\Node|null|int
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof FuncCall) {
            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
        }
        if ($node instanceof ConstFetch) {
            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
        }
        if (!$node instanceof Name) {
            return null;
        }
        if ($this->isNonNameNode($node)) {
            return null;
        }
        // class names itself are skipped automatically, as they are Identifier node
        $this->usedNames[] = $node->toString();
        return $node;
    }
    /**
     * @return string[]
     */
    public function getUsedNames() : array
    {
        $uniqueUsedNames = \array_unique($this->usedNames);
        \sort($uniqueUsedNames);
        return $uniqueUsedNames;
    }
    private function isNonNameNode(Name $name) : bool
    {
        // skip nodes that are not part of class names
        $parent = $name->getAttribute('parent');
        if ($parent instanceof Namespace_) {
            return \true;
        }
        return $parent instanceof ClassMethod;
    }
}
