<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak;

use PhpParser\NodeTraverser;
use PhpParser\Parser;
use RuntimeException;
use Throwable;
use TomasVotruba\ClassLeak\NodeDecorator\FullyQualifiedNameNodeDecorator;
use TomasVotruba\ClassLeak\NodeVisitor\UsedClassNodeVisitor;

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
    public function resolve(string $filePath): array
    {
        /** @var string $fileContents */
        $fileContents = file_get_contents($filePath);

        try {
            $stmts = $this->parser->parse($fileContents);
            if ($stmts === null) {
                return [];
            }
        } catch (Throwable $throwable) {
            throw new RuntimeException(sprintf(
                'Could not parse file "%s": %s',
                $filePath,
                $throwable->getMessage()
            ), $throwable->getCode(), $throwable);
        }

        $this->fullyQualifiedNameNodeDecorator->decorate($stmts);

        $nodeTraverser = new NodeTraverser();
        $usedClassNodeVisitor = new UsedClassNodeVisitor();
        $nodeTraverser->addVisitor($usedClassNodeVisitor);
        $nodeTraverser->traverse($stmts);

        return $usedClassNodeVisitor->getUsedNames();
    }
}
