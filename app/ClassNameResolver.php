<?php

declare (strict_types=1);
namespace TomasVotruba\ClassLeak;

use ClassLeak202307\PhpParser\NodeTraverser;
use ClassLeak202307\PhpParser\Parser;
use TomasVotruba\ClassLeak\NodeDecorator\FullyQualifiedNameNodeDecorator;
use TomasVotruba\ClassLeak\NodeVisitor\ClassNameNodeVisitor;
/**
 * @see \TomasVotruba\ClassLeak\Tests\ActiveClass\ClassNameResolver\ClassNameResolverTest
 */
final class ClassNameResolver
{
    /**
     * @readonly
     * @var \PhpParser\Parser
     */
    private $parser;
    /**
     * @readonly
     * @var \TomasVotruba\ClassLeak\NodeDecorator\FullyQualifiedNameNodeDecorator
     */
    private $fullyQualifiedNameNodeDecorator;
    public function __construct(Parser $parser, FullyQualifiedNameNodeDecorator $fullyQualifiedNameNodeDecorator)
    {
        $this->parser = $parser;
        $this->fullyQualifiedNameNodeDecorator = $fullyQualifiedNameNodeDecorator;
    }
    public function resolveFromFromFilePath(string $filePath) : ?string
    {
        /** @var string $fileContents */
        $fileContents = \file_get_contents($filePath);
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
