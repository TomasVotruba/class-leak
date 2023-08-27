<?php

declare (strict_types=1);
namespace TomasVotruba\ClassLeak\NodeVisitor;

use ClassLeak202308\PhpParser\Comment\Doc;
use ClassLeak202308\PhpParser\Node;
use ClassLeak202308\PhpParser\Node\Identifier;
use ClassLeak202308\PhpParser\Node\Name;
use ClassLeak202308\PhpParser\Node\Stmt\ClassLike;
use ClassLeak202308\PhpParser\NodeTraverser;
use ClassLeak202308\PhpParser\NodeVisitorAbstract;
final class ClassNameNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string
     * @see https://regex101.com/r/LXmPYG/1
     */
    private const API_TAG_REGEX = '#@api\\b#';
    /**
     * @var string|null
     */
    private $className = null;
    /**
     * @param Node\Stmt[] $nodes
     * @return Node\Stmt[]
     */
    public function beforeTraverse(array $nodes) : array
    {
        $this->className = null;
        return $nodes;
    }
    public function enterNode(Node $node) : ?int
    {
        if (!$node instanceof ClassLike) {
            return null;
        }
        if (!$node->name instanceof Identifier) {
            return null;
        }
        if ($this->hasApiTag($node)) {
            return null;
        }
        if (!$node->namespacedName instanceof Name) {
            return null;
        }
        $this->className = $node->namespacedName->toString();
        return NodeTraverser::DONT_TRAVERSE_CURRENT_AND_CHILDREN;
    }
    public function getClassName() : ?string
    {
        return $this->className;
    }
    private function hasApiTag(ClassLike $classLike) : bool
    {
        $doc = $classLike->getDocComment();
        if (!$doc instanceof Doc) {
            return \false;
        }
        \preg_match(self::API_TAG_REGEX, $doc->getText(), $matches);
        return $matches !== null;
    }
}
