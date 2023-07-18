<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak;

use PhpParser\NodeTraverser;
use PhpParser\Parser;
use TomasVotruba\ClassLeak\NodeDecorator\FullyQualifiedNameNodeDecorator;
use TomasVotruba\ClassLeak\NodeVisitor\UsedClassNodeVisitor;

/**
 * @see \TomasVotruba\ClassLeak\Tests\ActiveClass\UseImportsResolver\UseImportsResolverTest
 */
final class UseImportsResolver
{
    public function __construct(
        private readonly Parser $parser,
        private readonly FullyQualifiedNameNodeDecorator $fullyQualifiedNameNodeDecorator,
    ) {
    }

    //    /**
    //     * @param string[] $filePath
    //     * @return string[]
    //     */
    //    public function resolveFromFilePaths(array $filePaths): array
    //    {
    //        $usedNames = [];
    //
    //        foreach ($filePaths as $filePath) {
    //            $usedNames = array_merge($usedNames, $this->resolve($filePath));
    //        }
    //
    //        $usedNames = array_unique($usedNames);
    //        sort($usedNames);
    //
    //        return $usedNames;
    //    }

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
