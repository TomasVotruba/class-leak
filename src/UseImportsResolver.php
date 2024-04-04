<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak;

use PhpParser\NodeTraverser;
use PhpParser\Parser;
use TomasVotruba\ClassLeak\NodeDecorator\FullyQualifiedNameNodeDecorator;
use TomasVotruba\ClassLeak\NodeVisitor\UsedClassNodeVisitor;
use TomasVotruba\ClassLeak\ValueObject\FileWithClass;

/**
 * @see \TomasVotruba\ClassLeak\Tests\UseImportsResolver\UseImportsResolverTest
 */
final readonly class UseImportsResolver
{
    public function __construct(
        private Parser $parser,
        private FullyQualifiedNameNodeDecorator $fullyQualifiedNameNodeDecorator,
    ) {
    }

    /**
     * @return string[]
     */
    public function resolve(string $filePath, ?FileWithClass $fileWithClass): array
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

        $usedNames = $usedClassNodeVisitor->getUsedNames();

        foreach ($usedClassNodeVisitor->getUsedNamesInComments() as $usedNameInComment) {
            $usedNames[] = $fileWithClass?->resolveName($usedNameInComment) ?? $usedNameInComment;
        }

        $uniqueUsedNames = array_unique($usedNames);
        sort($uniqueUsedNames);

        return $uniqueUsedNames;
    }
}
