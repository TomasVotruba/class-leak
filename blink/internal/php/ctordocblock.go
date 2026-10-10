package php

import (
	"regexp"
	"strings"

	"github.com/rectorphp/php-parser-in-go/pkg/ast"
	"github.com/rectorphp/php-parser-in-go/pkg/visitor"
	"github.com/rectorphp/php-parser-in-go/pkg/visitor/nsresolver"
	"github.com/rectorphp/php-parser-in-go/pkg/visitor/traverser"
)

// type and name of "@param Type $name", type may contain spaces inside generics, e.g. "array<string, Foo>"
var paramTagRegex = regexp.MustCompile(`@param\s+((?:[^\s<]|<(?:[^<>]|<[^<>]*>)*>)+)\s+(?:\.\.\.)?\$(\w+)`)

// element type of "Foo[]"
var arraySuffixRegex = regexp.MustCompile(`(\\?[A-Za-z_][\w\\]*)\[\]`)

// value type of "array<Foo>", "iterable<int, Foo>", "list<Foo|Bar>" etc.
var genericCollectionRegex = regexp.MustCompile(`\b(?:non-empty-array|non-empty-list|array|iterable|list)<([^<>]+)>`)

var classNameRegex = regexp.MustCompile(`^\\?[A-Za-z_][\w\\]*$`)

var builtinDocTypes = map[string]bool{
	"array": true, "bool": true, "boolean": true, "callable": true, "false": true, "float": true,
	"int": true, "integer": true, "iterable": true, "mixed": true, "null": true, "object": true,
	"resource": true, "scalar": true, "self": true, "static": true, "string": true, "true": true,
	"void": true, "never": true,
}

// ResolveConstructorDocBlockTypes returns the FQN of collection element types in
// constructor @param tags, e.g. "@param TypeMapperInterface[] $typeMappers", unique
// in first-seen order. Matches the PHP ConstructorParamTypeNodeVisitor.
func ResolveConstructorDocBlockTypes(pf *ParsedFile) []string {
	collector := &docBlockCollector{NamespaceResolver: nsresolver.NewNamespaceResolver(), seen: map[string]bool{}}
	traverser.NewTraverser(collector).Traverse(pf.Root)
	return collector.result
}

// docBlockCollector resolves names while traversing, so the namespace and use
// imports are current when a constructor docblock is read.
type docBlockCollector struct {
	*nsresolver.NamespaceResolver
	result []string
	seen   map[string]bool
}

func (collector *docBlockCollector) StmtClassMethod(node *ast.StmtClassMethod) {
	collector.NamespaceResolver.StmtClassMethod(node)

	if !isConstructor(node.Name) {
		return
	}

	paramNames := map[string]bool{}
	for _, p := range node.Params {
		param, ok := p.(*ast.Parameter)
		if !ok {
			continue
		}
		if variable, ok := param.Var.(*ast.ExprVariable); ok {
			if id, ok := variable.Name.(*ast.Identifier); ok {
				paramNames[strings.TrimPrefix(string(id.Value), "$")] = true
			}
		}
	}

	for _, match := range paramTagRegex.FindAllStringSubmatch(visitor.GetDocCommentText(node), -1) {
		if !paramNames[match[2]] {
			continue
		}
		for _, elementName := range collectionElementNames(match[1]) {
			fqn := collector.resolveDocBlockName(elementName)
			if !collector.seen[fqn] {
				collector.seen[fqn] = true
				collector.result = append(collector.result, fqn)
			}
		}
	}
}

func (collector *docBlockCollector) resolveDocBlockName(name string) string {
	if strings.HasPrefix(name, "\\") {
		return strings.TrimPrefix(name, "\\")
	}

	var parts []ast.Vertex
	for _, part := range strings.Split(name, "\\") {
		parts = append(parts, &ast.NamePart{Value: []byte(part)})
	}

	fqn, err := collector.Namespace.ResolveName(&ast.Name{Parts: parts}, "")
	if err != nil {
		return name
	}
	return fqn
}

func collectionElementNames(docType string) []string {
	var candidates []string
	for _, match := range arraySuffixRegex.FindAllStringSubmatch(docType, -1) {
		candidates = append(candidates, match[1])
	}
	for _, match := range genericCollectionRegex.FindAllStringSubmatch(docType, -1) {
		// value type is the last argument, key type comes first
		args := strings.Split(match[1], ",")
		for _, name := range strings.Split(args[len(args)-1], "|") {
			candidates = append(candidates, strings.TrimSpace(name))
		}
	}

	var names []string
	for _, name := range candidates {
		if classNameRegex.MatchString(name) && !builtinDocTypes[strings.ToLower(name)] {
			names = append(names, name)
		}
	}
	return names
}
