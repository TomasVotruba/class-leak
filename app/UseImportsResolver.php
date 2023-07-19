<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak;

use PhpParser\NodeTraverser;
use PhpParser\Parser;
use TomasVotruba\ClassLeak\NodeDecorator\FullyQualifiedNameNodeDecorator;
use TomasVotruba\ClassLeak\NodeVisitor\UsedClassNodeVisitor;

/**
 * @see \TomasVotruba\ClassLeak\Tests\UseImportsResolver\UseImportsResolverTest
 */
final class UseImportsResolver
{
    public function __construct(
        private readonly Parser $parser,
        private readonly FullyQualifiedNameNodeDecorator $fullyQualifiedNameNodeDecorator,
    ) {
    }

    /**
     * @return string[]
     */
    public function resolve(string $filePath): array
    {
        /** @var string $fileContents */
        $fileContents = file_get_contents($filePath);

        $stmts = $this->parser->parse($fileContents);
        if ($stmts === null) {
            return [];
        }

        $this->fullyQualifiedNameNodeDecorator->decorate($stmts);

        $nodeTraverser = new NodeTraverser();
        $usedClassNodeVisitor = new UsedClassNodeVisitor();
        $nodeTraverser->addVisitor($usedClassNodeVisitor);
        $nodeTraverser->traverse($stmts);

        return $usedClassNodeVisitor->getUsedNames();
    }
}
