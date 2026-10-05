package php

import (
	"regexp"

	"github.com/rectorphp/php-parser-in-go/pkg/ast"
	"github.com/rectorphp/php-parser-in-go/pkg/visitor"
	"github.com/tomasvotruba/class-leak/go/internal/model"
)

var apiTagRegex = regexp.MustCompile(`@api\b`)

// ResolveClassInfo extracts the declared class-like of a file. When several
// class-likes share a file the last named one wins for name and flags while
// attributes accumulate, matching the PHP visitor. A class tagged @api, or a
// file with no named class-like, yields ok=false.
func ResolveClassInfo(pf *ParsedFile) (model.ClassNames, bool) {
	var result model.ClassNames
	found := false

	for _, stmt := range classLikeStmts(pf.Root) {
		name, ok := classLikeName(stmt, pf.Resolved)
		if !ok {
			continue
		}
		if apiTagRegex.MatchString(visitor.GetDocCommentText(stmt)) {
			continue
		}

		found = true
		result.ClassName = name

		switch node := stmt.(type) {
		case *ast.StmtClass:
			result.Kind = model.KindClass
			if node.Extends != nil {
				result.HasParentClassOrIface = true
			}
			if len(node.Implements) > 0 {
				result.HasParentClassOrIface = true
				for _, impl := range node.Implements {
					if fqn, ok := pf.Resolved[impl]; ok {
						result.InterfaceNames = append(result.InterfaceNames, fqn)
					}
				}
			}
			result.Attributes = appendAttrNames(result.Attributes, node.AttrGroups, pf.Resolved)
			for _, s := range node.Stmts {
				if method, ok := s.(*ast.StmtClassMethod); ok {
					result.Attributes = appendAttrNames(result.Attributes, method.AttrGroups, pf.Resolved)
				}
			}
		case *ast.StmtInterface:
			result.Kind = model.KindInterface
			if len(node.Extends) > 0 {
				result.HasParentClassOrIface = true
			}
			result.Attributes = appendAttrNames(result.Attributes, node.AttrGroups, pf.Resolved)
		case *ast.StmtTrait:
			result.Kind = model.KindTrait
			result.Attributes = appendAttrNames(result.Attributes, node.AttrGroups, pf.Resolved)
		case *ast.StmtEnum:
			result.Kind = model.KindEnum
			result.Attributes = appendAttrNames(result.Attributes, node.AttrGroups, pf.Resolved)
		}
	}

	return result, found
}

// classLikeStmts returns top-level and namespaced class-like statements.
func classLikeStmts(root ast.Vertex) []ast.Vertex {
	r, ok := root.(*ast.Root)
	if !ok {
		return nil
	}
	var stmts []ast.Vertex
	for _, s := range r.Stmts {
		if ns, ok := s.(*ast.StmtNamespace); ok {
			stmts = append(stmts, ns.Stmts...)
			continue
		}
		stmts = append(stmts, s)
	}
	return stmts
}

// classLikeName returns the resolved FQN of a class-like node. ok is false for
// a non-class-like or anonymous (unresolved) declaration.
func classLikeName(stmt ast.Vertex, resolved map[ast.Vertex]string) (string, bool) {
	switch stmt.(type) {
	case *ast.StmtClass, *ast.StmtInterface, *ast.StmtTrait, *ast.StmtEnum:
		fqn, ok := resolved[stmt]
		return fqn, ok
	}
	return "", false
}

// appendAttrNames resolves attribute names of the given groups and appends the
// unique ones, preserving order.
func appendAttrNames(existing []string, groups []ast.Vertex, resolved map[ast.Vertex]string) []string {
	for _, g := range groups {
		ag, ok := g.(*ast.AttributeGroup)
		if !ok {
			continue
		}
		for _, a := range ag.Attrs {
			attr, ok := a.(*ast.Attribute)
			if !ok || attr.Name == nil {
				continue
			}
			fqn, ok := resolved[attr.Name]
			if !ok || fqn == "" {
				continue
			}
			if !contains(existing, fqn) {
				existing = append(existing, fqn)
			}
		}
	}
	return existing
}

func contains(list []string, value string) bool {
	for _, v := range list {
		if v == value {
			return true
		}
	}
	return false
}
