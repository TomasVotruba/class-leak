package php

import (
	"sort"
	"strings"

	"github.com/rectorphp/php-parser-in-go/pkg/ast"
	"github.com/rectorphp/php-parser-in-go/pkg/visitor"
	"github.com/rectorphp/php-parser-in-go/pkg/visitor/traverser"
)

// ResolveUsedNames returns the FQN of every class-name reference in a file,
// sorted and unique. Names under a namespace declaration, a function call, or a
// method name are skipped, as are const-fetch subtrees, matching the PHP visitor.
func ResolveUsedNames(pf *ParsedFile) []string {
	uv := &usedNamesVisitor{
		Null:     &visitor.Null{},
		resolved: pf.Resolved,
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
	skip     map[ast.Vertex]bool
	used     map[string]bool
}

// Attribute names are resolved into the resolved map by the upstream namespace
// resolver, so they are picked up here to count attribute usage.
func (v *usedNamesVisitor) Attribute(n *ast.Attribute) {
	if n.Name == nil {
		return
	}
	v.skip[n.Name] = true
	if fqn, ok := v.resolved[n.Name]; ok && !ast.IsReservedType(fqn) {
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

// Imported names are not in the resolved map, so they are recorded here to
// count a "use" import as usage, matching the PHP visitor.
func (v *usedNamesVisitor) StmtUse(n *ast.StmtUseList) {
	for _, use := range n.Uses {
		v.recordImport(nil, use)
	}
}

func (v *usedNamesVisitor) StmtGroupUse(n *ast.StmtGroupUseList) {
	for _, use := range n.Uses {
		v.recordImport(nameParts(n.Prefix), use)
	}
}

func (v *usedNamesVisitor) recordImport(prefix []ast.Vertex, use ast.Vertex) {
	stmtUse, ok := use.(*ast.StmtUse)
	if !ok {
		return
	}

	parts := append(append([]ast.Vertex{}, prefix...), nameParts(stmtUse.Use)...)
	if len(parts) == 0 {
		return
	}

	var segments []string
	for _, part := range parts {
		if namePart, ok := part.(*ast.NamePart); ok {
			segments = append(segments, string(namePart.Value))
		}
	}
	v.used[strings.Join(segments, "\\")] = true
}

func nameParts(node ast.Vertex) []ast.Vertex {
	switch name := node.(type) {
	case *ast.Name:
		return name.Parts
	case *ast.NameRelative:
		return name.Parts
	case *ast.NameFullyQualified:
		return name.Parts
	}
	return nil
}

func (v *usedNamesVisitor) record(n ast.Vertex) {
	if v.skip[n] {
		return
	}
	fqn, ok := v.resolved[n]
	if !ok || ast.IsReservedType(fqn) {
		return
	}
	v.used[fqn] = true
}

func (v *usedNamesVisitor) NameName(n *ast.Name)                         { v.record(n) }
func (v *usedNamesVisitor) NameFullyQualified(n *ast.NameFullyQualified) { v.record(n) }
func (v *usedNamesVisitor) NameRelative(n *ast.NameRelative)             { v.record(n) }
