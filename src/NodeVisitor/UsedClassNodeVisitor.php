<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;
use TomasVotruba\ClassLeak\PhpDocNodeVisitor\UsedTypeNodeVisitor;

final class UsedClassNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string[]
     */
    private const TYPES_TO_SKIP = [
        'list',
        'array',
        'int',
        'string',
        'bool',
        'float',
        'object',
        'mixed',
        'null',
        'void',
        'callable',
        'iterable',
        'self',
        'static',
        'true',
        'false',
        'resource',
    ];

    /**
     * @var string[]
     */
    private array $usedNames = [];

    /**
     * @var string[]
     */
    private array $comments = [];

    private readonly Lexer $lexer;

    private readonly PhpDocParser $phpDocParser;

    public function __construct()
    {
        $constExprParser = new ConstExprParser();

        $this->lexer = new Lexer();
        $this->phpDocParser = new PhpDocParser(new TypeParser($constExprParser), $constExprParser);
    }

    /**
     * @param Stmt[] $nodes
     * @return Stmt[]
     */
    public function beforeTraverse(array $nodes): array
    {
        $this->usedNames = [];
        $this->comments = [];

        return $nodes;
    }

    public function enterNode(Node $node): Node|null|int
    {
        if ($node instanceof ConstFetch) {
            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
        }

        if ($node instanceof ClassLike) {
            if ($node->getDocComment() !== null) {
                $this->comments[] = $node->getDocComment()->getText();
            }

            foreach ($node->getMethods() as $classMethod) {
                if ($classMethod->getDocComment() !== null) {
                    $this->comments[] = $classMethod->getDocComment()->getText();
                }
            }
        }

        if (! $node instanceof Name) {
            return null;
        }

        if ($this->isNonNameNode($node)) {
            return null;
        }

        // class names itself are skipped automatically, as they are Identifier node

        $this->usedNames[] = $node->toString();

        return $node;
    }

    /**
     * @return string[]
     */
    public function getUsedNames(): array
    {
        return $this->usedNames;
    }

    /**
     * @return string[]
     */
    public function getUsedNamesInComments(): array
    {
        if ($this->comments === []) {
            return [];
        }

        $tokens = $this->lexer->tokenize(implode(PHP_EOL, $this->comments));
        $phpDocNode = $this->phpDocParser->parse(new TokenIterator($tokens));
        $usedTypeNodeVisitor = new UsedTypeNodeVisitor();
        $nodeTraverser = new \PHPStan\PhpDocParser\Ast\NodeTraverser([$usedTypeNodeVisitor]);
        $nodeTraverser->traverse([$phpDocNode]);

        $filteredTypes = array_diff($usedTypeNodeVisitor->getUsedTypes(), self::TYPES_TO_SKIP);

        return array_unique($filteredTypes);
    }

    private function isNonNameNode(Name $name): bool
    {
        // skip nodes that are not part of class names
        $parent = $name->getAttribute('parent');
        if ($parent instanceof Namespace_) {
            return true;
        }

        if ($parent instanceof FuncCall) {
            return true;
        }

        return $parent instanceof ClassMethod;
    }
}
