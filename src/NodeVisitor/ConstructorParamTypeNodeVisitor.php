<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\NodeVisitor;

use PhpParser\Comment\Doc;
use PhpParser\NameContext;
use PhpParser\Node;
use PhpParser\Node\ComplexType;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\IntersectionType;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\NullableType;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\UnionType;
use PhpParser\NodeVisitorAbstract;

final class ConstructorParamTypeNodeVisitor extends NodeVisitorAbstract
{
    /**
     * Type and name of "@param Type $name", type may contain spaces inside generics, e.g. "array<string, Foo>"
     */
    private const PARAM_TAG_REGEX = '#@param\s+(?<type>(?:[^\s<]|<(?:[^<>]|<[^<>]*>)*>)+)\s+(?:\.\.\.)?\$(?<name>\w+)#';

    /**
     * Element type of "Foo[]"
     */
    private const ARRAY_SUFFIX_REGEX = '#(?<name>\\\\?[A-Za-z_][\w\\\\]*)\[\]#';

    /**
     * Value type of "array<Foo>", "iterable<int, Foo>", "list<Foo|Bar>" etc.
     */
    private const GENERIC_COLLECTION_REGEX = '#\b(?:non-empty-array|non-empty-list|array|iterable|list)<(?<args>[^<>]+)>#';

    /**
     * @var string[]
     */
    private const BUILTIN_TYPES = [
        'array', 'bool', 'boolean', 'callable', 'false', 'float', 'int', 'integer', 'iterable', 'mixed',
        'null', 'object', 'resource', 'scalar', 'self', 'static', 'string', 'true', 'void', 'never',
    ];

    /**
     * @var string[]
     */
    private array $paramTypeNames = [];

    /**
     * Shared with the NameResolver running in the same traverser, to resolve docblock short names
     */
    private ?NameContext $nameContext;

    public function __construct(?NameContext $nameContext = null)
    {
        $this->nameContext = $nameContext;
    }

    /**
     * @param Stmt[] $nodes
     * @return Stmt[]
     */
    public function beforeTraverse(array $nodes): array
    {
        $this->paramTypeNames = [];
        return $nodes;
    }

    public function enterNode(Node $node)
    {
        if (! $node instanceof ClassMethod) {
            return null;
        }

        if ($node->name->toLowerString() !== '__construct') {
            return null;
        }

        foreach ($node->params as $param) {
            if ($param->type === null) {
                continue;
            }

            foreach ($this->resolveTypeNames($param->type) as $typeName) {
                $this->paramTypeNames[] = $typeName;
            }
        }

        foreach ($this->resolveDocBlockCollectionTypeNames($node) as $typeName) {
            $this->paramTypeNames[] = $typeName;
        }

        return null;
    }

    /**
     * @return string[]
     */
    public function getParamTypeNames(): array
    {
        return array_values(array_unique($this->paramTypeNames));
    }

    /**
     * @param Node\Identifier|Name|ComplexType $type
     * @return string[]
     */
    private function resolveTypeNames(Node $type): array
    {
        if ($type instanceof Name) {
            return [$type->toString()];
        }

        if ($type instanceof NullableType) {
            return $this->resolveTypeNames($type->type);
        }

        if ($type instanceof UnionType || $type instanceof IntersectionType) {
            $typeNames = [];
            foreach ($type->types as $innerType) {
                $typeNames = [...$typeNames, ...$this->resolveTypeNames($innerType)];
            }

            return $typeNames;
        }

        // builtin Identifier type, e.g. string, int
        return [];
    }

    /**
     * Collections injected by the container, e.g. "@param TypeMapperInterface[] $typeMappers"
     *
     * @return string[]
     */
    private function resolveDocBlockCollectionTypeNames(ClassMethod $classMethod): array
    {
        if (! $this->nameContext instanceof NameContext) {
            return [];
        }

        $doc = $classMethod->getDocComment();
        if (! $doc instanceof Doc) {
            return [];
        }

        $paramNames = [];
        foreach ($classMethod->params as $param) {
            if ($param->var instanceof Variable && is_string($param->var->name)) {
                $paramNames[] = $param->var->name;
            }
        }

        preg_match_all(self::PARAM_TAG_REGEX, $doc->getText(), $paramTagMatches, PREG_SET_ORDER);

        $typeNames = [];
        foreach ($paramTagMatches as $paramTagMatch) {
            if (! in_array($paramTagMatch['name'], $paramNames, true)) {
                continue;
            }

            foreach ($this->matchCollectionElementNames($paramTagMatch['type']) as $elementName) {
                $typeNames[] = $this->resolveDocBlockName($elementName);
            }
        }

        return $typeNames;
    }

    /**
     * @return string[]
     */
    private function matchCollectionElementNames(string $type): array
    {
        $elementNames = [];

        preg_match_all(self::ARRAY_SUFFIX_REGEX, $type, $arraySuffixMatches);
        $elementNames = $arraySuffixMatches['name'];

        preg_match_all(self::GENERIC_COLLECTION_REGEX, $type, $genericMatches);
        foreach ($genericMatches['args'] as $args) {
            // value type is the last argument, key type comes first
            $argParts = explode(',', $args);
            $valueType = end($argParts);

            foreach (explode('|', $valueType) as $name) {
                $elementNames[] = trim($name);
            }
        }

        return array_filter(
            $elementNames,
            static fn (string $name): bool => preg_match('#^\\\\?[A-Za-z_][\w\\\\]*$#', $name) === 1
                && ! in_array(strtolower($name), self::BUILTIN_TYPES, true)
        );
    }

    private function resolveDocBlockName(string $name): string
    {
        /** @var NameContext $nameContext */
        $nameContext = $this->nameContext;

        $nameNode = str_starts_with($name, '\\') ? new FullyQualified(ltrim($name, '\\')) : new Name($name);

        return $nameContext->getResolvedClassName($nameNode)->toString();
    }
}
