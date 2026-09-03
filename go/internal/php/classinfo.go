package php

import (
	"regexp"

	"github.com/rectorphp/php-parser-in-go/pkg/ast"
	"github.com/rectorphp/php-parser-in-go/pkg/token"
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
	nr := newNameResolver(pf.Root)

	for _, stmt := range classLikeStmts(pf.Root) {
		leading, name, ok := classLikeMeta(stmt, pf.Resolved)
		if !ok {
			continue
		}
		if hasApiTag(leading) {
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
			result.Attributes = appendAttrNames(result.Attributes, node.AttrGroups, nr)
			for _, s := range node.Stmts {
				if method, ok := s.(*ast.StmtClassMethod); ok {
					result.Attributes = appendAttrNames(result.Attributes, method.AttrGroups, nr)
				}
			}
		case *ast.StmtInterface:
			result.Kind = model.KindInterface
			if len(node.Extends) > 0 {
				result.HasParentClassOrIface = true
			}
			result.Attributes = appendAttrNames(result.Attributes, node.AttrGroups, nr)
		case *ast.StmtTrait:
			result.Kind = model.KindTrait
			result.Attributes = appendAttrNames(result.Attributes, node.AttrGroups, nr)
		case *ast.StmtEnum:
			result.Kind = model.KindEnum
			result.Attributes = appendAttrNames(result.Attributes, node.AttrGroups, nr)
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

// classLikeMeta returns the leading tokens (for doc-comment lookup) and the
// resolved FQN of a class-like node. ok is false for non-class-like or
// anonymous (unresolved) declarations.
func classLikeMeta(stmt ast.Vertex, resolved map[ast.Vertex]string) (leading []*token.Token, name string, ok bool) {
	fqn, hasName := resolved[stmt]
	if !hasName {
		return nil, "", false
	}

	switch node := stmt.(type) {
	case *ast.StmtClass:
		leading = append(leading, attrGroupTokens(node.AttrGroups)...)
		leading = append(leading, modifierTokens(node.Modifiers)...)
		leading = append(leading, node.ClassTkn)
	case *ast.StmtInterface:
		leading = append(leading, attrGroupTokens(node.AttrGroups)...)
		leading = append(leading, node.InterfaceTkn)
	case *ast.StmtTrait:
		leading = append(leading, attrGroupTokens(node.AttrGroups)...)
		leading = append(leading, node.TraitTkn)
	case *ast.StmtEnum:
		leading = append(leading, attrGroupTokens(node.AttrGroups)...)
		leading = append(leading, node.EnumTkn)
	default:
		return nil, "", false
	}
	return leading, fqn, true
}

func attrGroupTokens(groups []ast.Vertex) []*token.Token {
	var tokens []*token.Token
	for _, g := range groups {
		if ag, ok := g.(*ast.AttributeGroup); ok {
			tokens = append(tokens, ag.OpenAttributeTkn)
		}
	}
	return tokens
}

func modifierTokens(modifiers []ast.Vertex) []*token.Token {
	var tokens []*token.Token
	for _, m := range modifiers {
		if id, ok := m.(*ast.Identifier); ok {
			tokens = append(tokens, id.IdentifierTkn)
		}
	}
	return tokens
}

func hasApiTag(leading []*token.Token) bool {
	for _, t := range leading {
		if t == nil {
			continue
		}
		for _, ff := range t.FreeFloating {
			if ff.ID == token.T_DOC_COMMENT && apiTagRegex.Match(ff.Value) {
				return true
			}
		}
	}
	return false
}

// appendAttrNames resolves attribute names of the given groups and appends the
// unique ones, preserving order.
func appendAttrNames(existing []string, groups []ast.Vertex, nr *nameResolver) []string {
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
			fqn := nr.resolve(attr.Name)
			if fqn == "" {
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
