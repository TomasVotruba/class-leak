<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak;

use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use TomasVotruba\ClassLeak\NodeVisitor\ConstructorParamTypeNodeVisitor;

final class ConstructorParamTypeResolver
{
    private Parser $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
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

        // same traverser, so the name context is current when the constructor docblock is read
        $nameResolver = new NameResolver();
        $constructorParamTypeNodeVisitor = new ConstructorParamTypeNodeVisitor($nameResolver->getNameContext());

        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($nameResolver);
        $nodeTraverser->addVisitor($constructorParamTypeNodeVisitor);
        $nodeTraverser->traverse($stmts);

        return $constructorParamTypeNodeVisitor->getParamTypeNames();
    }
}
