<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak;

use PhpParser\NodeTraverser;
use PhpParser\Parser;
use TomasVotruba\ClassLeak\NodeDecorator\FullyQualifiedNameNodeDecorator;
use TomasVotruba\ClassLeak\NodeVisitor\ConstructorParamTypeNodeVisitor;

/**
 * @see \TomasVotruba\ClassLeak\Tests\ConstructorParamTypeResolver\ConstructorParamTypeResolverTest
 */
final readonly class ConstructorParamTypeResolver
{
    public function __construct(
        private Parser $parser,
        private FullyQualifiedNameNodeDecorator $fullyQualifiedNameNodeDecorator,
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

        $constructorParamTypeNodeVisitor = new ConstructorParamTypeNodeVisitor();
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($constructorParamTypeNodeVisitor);
        $nodeTraverser->traverse($stmts);

        return $constructorParamTypeNodeVisitor->getParamTypeNames();
    }
}
