<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\PhpDocNodeVisitor;

use PHPStan\PhpDocParser\Ast\AbstractNodeVisitor;
use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;

final class UsedTypeNodeVisitor extends AbstractNodeVisitor
{
    /**
     * @var string[]
     */
    private array $usedTypes = [];

    public function beforeTraverse(array $nodes): ?array
    {
        $this->usedTypes = [];
        return null;
    }

    public function enterNode(Node $node)
    {
        if (! $node instanceof IdentifierTypeNode) {
            return null;
        }

        $this->usedTypes[] = $node->name;

        return null;
    }

    /**
     * @return string[]
     */
    public function getUsedTypes(): array
    {
        return $this->usedTypes;
    }
}
