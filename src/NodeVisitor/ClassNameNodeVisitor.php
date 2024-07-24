<?php

declare (strict_types=1);
namespace TomasVotruba\ClassLeak\NodeVisitor;

use ClassLeak202407\PhpParser\Comment\Doc;
use ClassLeak202407\PhpParser\Node;
use ClassLeak202407\PhpParser\Node\Identifier;
use ClassLeak202407\PhpParser\Node\Name;
use ClassLeak202407\PhpParser\Node\Stmt\Class_;
use ClassLeak202407\PhpParser\Node\Stmt\ClassLike;
use ClassLeak202407\PhpParser\Node\Stmt\Interface_;
use ClassLeak202407\PhpParser\NodeTraverser;
use ClassLeak202407\PhpParser\NodeVisitorAbstract;
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
     * @var bool
     */
    private $hasParentClassOrInterface = \false;
    /**
     * @var string[]
     */
    private $attributes = [];
    /**
     * @param Node\Stmt[] $nodes
     * @return Node\Stmt[]
     */
    public function beforeTraverse(array $nodes) : array
    {
        $this->className = null;
        $this->hasParentClassOrInterface = \false;
        $this->attributes = [];
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
        if ($node instanceof Class_) {
            if ($node->extends instanceof Name) {
                $this->hasParentClassOrInterface = \true;
            }
            if ($node->implements !== []) {
                $this->hasParentClassOrInterface = \true;
            }
        }
        if ($node instanceof Interface_ && $node->extends !== []) {
            $this->hasParentClassOrInterface = \true;
        }
        foreach ($node->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attr) {
                $this->attributes[] = $attr->name->toString();
            }
        }
        foreach ($node->getMethods() as $classMethod) {
            foreach ($classMethod->attrGroups as $attrGroup) {
                foreach ($attrGroup->attrs as $attr) {
                    $this->attributes[] = $attr->name->toString();
                }
            }
        }
        return NodeTraverser::DONT_TRAVERSE_CURRENT_AND_CHILDREN;
    }
    public function getClassName() : ?string
    {
        return $this->className;
    }
    public function hasParentClassOrInterface() : bool
    {
        return $this->hasParentClassOrInterface;
    }
    /**
     * @return string[]
     */
    public function getAttributes() : array
    {
        return \array_unique($this->attributes);
    }
    private function hasApiTag(ClassLike $classLike) : bool
    {
        $doc = $classLike->getDocComment();
        if (!$doc instanceof Doc) {
            return \false;
        }
        return \preg_match(self::API_TAG_REGEX, $doc->getText(), $matches) === 1;
    }
}
