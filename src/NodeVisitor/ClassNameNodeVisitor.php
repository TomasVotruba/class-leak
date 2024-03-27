<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\NodeVisitor;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

final class ClassNameNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string
     * @see https://regex101.com/r/LXmPYG/1
     */
    private const API_TAG_REGEX = '#@api\b#';

    private string|null $className = null;

    private bool $hasParentClassOrInterface = false;

    private bool $hasApiTag = false;

    /**
     * @var string[]
     */
    private array $usedAttributes = [];

    /**
     * @param Node\Stmt[] $nodes
     * @return Node\Stmt[]
     */
    public function beforeTraverse(array $nodes): array
    {
        $this->className = null;
        $this->hasParentClassOrInterface = false;
        $this->hasApiTag = false;
        $this->usedAttributes = [];

        return $nodes;
    }

    public function enterNode(Node $node): ?int
    {
        if (! $node instanceof ClassLike) {
            return null;
        }

        if (! $node->name instanceof Identifier) {
            return null;
        }

        if ($this->doesClassHaveApiTag($node)) {
            $this->hasApiTag = true;
        }

        if (! $node->namespacedName instanceof Name) {
            return null;
        }

        $this->className = $node->namespacedName->toString();
        if ($node instanceof Class_) {
            if ($node->extends instanceof Name) {
                $this->hasParentClassOrInterface = true;
            }

            if ($node->implements !== []) {
                $this->hasParentClassOrInterface = true;
            }
        }

        if ($node instanceof Interface_ && $node->extends !== []) {
            $this->hasParentClassOrInterface = true;
        }

        foreach ($node->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attr) {
                $this->usedAttributes[] = $attr->name->toString();
            }
        }

        foreach ($node->getMethods() as $classMethod) {
            foreach ($classMethod->attrGroups as $attrGroup) {
                foreach ($attrGroup->attrs as $attr) {
                    $this->usedAttributes[] = $attr->name->toString();
                }
            }
        }

        return NodeTraverser::DONT_TRAVERSE_CURRENT_AND_CHILDREN;
    }

    public function getClassName(): ?string
    {
        return $this->className;
    }

    public function hasParentClassOrInterface(): bool
    {
        return $this->hasParentClassOrInterface;
    }

    public function hasApiTag() : bool
    {
        return $this->hasApiTag;
    }

    /**
     * @return string[]
     */
    public function getUsedAttributes(): array
    {
        return array_unique($this->usedAttributes);
    }

    private function doesClassHaveApiTag(ClassLike $classLike): bool
    {
        $doc = $classLike->getDocComment();
        if (! $doc instanceof Doc) {
            return false;
        }

        return preg_match(self::API_TAG_REGEX, $doc->getText()) === 1;
    }
}
