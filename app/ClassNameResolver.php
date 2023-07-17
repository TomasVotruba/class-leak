<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak;

use Nette\Utils\FileSystem;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use Symfony\Component\Finder\SplFileInfo;
use TomasVotruba\ClassLeak\NodeDecorator\FullyQualifiedNameNodeDecorator;
use TomasVotruba\ClassLeak\NodeVisitor\ClassNameNodeVisitor;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \TomasVotruba\ClassLeak\Tests\ActiveClass\ClassNameResolver\ClassNameResolverTest
 */
final class ClassNameResolver
{
    public function __construct(
        private readonly Parser $parser,
        private readonly FullyQualifiedNameNodeDecorator $fullyQualifiedNameNodeDecorator
    ) {
    }

    /**
     * @api
     */
    public function resolveFromFromFilePath(string $filePath): ?string
    {
        $fileContents = FileSystem::read($filePath);

        $stmts = $this->parser->parse($fileContents);
        if ($stmts === null) {
            return null;
        }

        $this->fullyQualifiedNameNodeDecorator->decorate($stmts);

        $classNameNodeVisitor = new ClassNameNodeVisitor();
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($classNameNodeVisitor);
        $nodeTraverser->traverse($stmts);

        return $classNameNodeVisitor->getClassName();
    }
}
