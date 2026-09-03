package php

import (
	"sort"

	"github.com/rectorphp/php-parser-in-go/pkg/ast"
	"github.com/rectorphp/php-parser-in-go/pkg/visitor"
	"github.com/rectorphp/php-parser-in-go/pkg/visitor/traverser"
)

// builtinTypes are reserved type keywords that are not class references. The
// upstream parser resolves them as names, unlike nikic where they are plain
// identifiers, so they are filtered out to match the PHP tool.
var builtinTypes = map[string]bool{
	"int": true, "float": true, "string": true, "bool": true, "void": true,
	"array": true, "iterable": true, "callable": true, "object": true,
	"mixed": true, "never": true, "null": true, "false": true, "true": true,
	"self": true, "static": true, "parent": true,
}

// ResolveUsedNames returns the FQN of every class-name reference in a file,
// sorted and unique. Names under a namespace declaration, a function call, or a
// method name are skipped, as are const-fetch subtrees, matching the PHP visitor.
func ResolveUsedNames(pf *ParsedFile) []string {
	uv := &usedNamesVisitor{
		Null:     &visitor.Null{},
		resolved: pf.Resolved,
		nr:       newNameResolver(pf.Root),
		skip:     map[ast.Vertex]bool{},
		used:     map[string]bool{},
	}
	traverser.NewTraverser(uv).Traverse(pf.Root)

	names := make([]string, 0, len(uv.used))
	for name := range uv.used {
		names = append(names, name)
	}
	sort.Strings(names)
	return names
}

type usedNamesVisitor struct {
	*visitor.Null
	resolved map[ast.Vertex]string
	nr       *nameResolver
	skip     map[ast.Vertex]bool
	used     map[string]bool
}

// Attribute names are not resolved by the upstream namespace resolver, so they
// are collected here via the local resolver to count attribute usage.
func (v *usedNamesVisitor) Attribute(n *ast.Attribute) {
	if n.Name == nil {
		return
	}
	v.skip[n.Name] = true
	if fqn := v.nr.resolve(n.Name); fqn != "" && !builtinTypes[fqn] {
		v.used[fqn] = true
	}
}

func (v *usedNamesVisitor) StmtNamespace(n *ast.StmtNamespace) {
	if n.Name != nil {
		v.skip[n.Name] = true
	}
}

func (v *usedNamesVisitor) ExprFunctionCall(n *ast.ExprFunctionCall) {
	if n.Function != nil {
		v.skip[n.Function] = true
	}
}

func (v *usedNamesVisitor) ExprConstFetch(n *ast.ExprConstFetch) {
	if n.Const != nil {
		v.skip[n.Const] = true
	}
}

func (v *usedNamesVisitor) record(n ast.Vertex) {
	if v.skip[n] {
		return
	}
	fqn, ok := v.resolved[n]
	if !ok || builtinTypes[fqn] {
		return
	}
	v.used[fqn] = true
}

func (v *usedNamesVisitor) NameName(n *ast.Name)                         { v.record(n) }
func (v *usedNamesVisitor) NameFullyQualified(n *ast.NameFullyQualified) { v.record(n) }
func (v *usedNamesVisitor) NameRelative(n *ast.NameRelative)             { v.record(n) }
