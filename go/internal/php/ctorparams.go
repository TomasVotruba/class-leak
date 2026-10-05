package php

import (
	"github.com/rectorphp/php-parser-in-go/pkg/ast"
)

// ResolveConstructorParamTypes returns the FQN of class types type-hinted on
// constructor parameters, unique in first-seen order. Scalar types are skipped;
// nullable, union and intersection types are unwrapped.
func ResolveConstructorParamTypes(pf *ParsedFile) []string {
	var result []string
	seen := map[string]bool{}

	for _, stmt := range classLikeStmts(pf.Root) {
		for _, s := range classLikeBodyStmts(stmt) {
			method, ok := s.(*ast.StmtClassMethod)
			if !ok {
				continue
			}
			if !isConstructor(method.Name) {
				continue
			}
			for _, p := range method.Params {
				param, ok := p.(*ast.Parameter)
				if !ok || param.Type == nil {
					continue
				}
				for _, fqn := range typeNames(param.Type, pf.Resolved) {
					if !seen[fqn] {
						seen[fqn] = true
						result = append(result, fqn)
					}
				}
			}
		}
	}
	return result
}

func classLikeBodyStmts(stmt ast.Vertex) []ast.Vertex {
	switch node := stmt.(type) {
	case *ast.StmtClass:
		return node.Stmts
	case *ast.StmtInterface:
		return node.Stmts
	case *ast.StmtTrait:
		return node.Stmts
	case *ast.StmtEnum:
		return node.Stmts
	}
	return nil
}

func isConstructor(name ast.Vertex) bool {
	id, ok := name.(*ast.Identifier)
	if !ok {
		return false
	}
	return equalFold(string(id.Value), "__construct")
}

func typeNames(t ast.Vertex, resolved map[ast.Vertex]string) []string {
	switch node := t.(type) {
	case *ast.Name, *ast.NameFullyQualified, *ast.NameRelative:
		if fqn, ok := resolved[t]; ok && !ast.IsReservedType(fqn) {
			return []string{fqn}
		}
	case *ast.Nullable:
		return typeNames(node.Expr, resolved)
	case *ast.Union:
		var names []string
		for _, inner := range node.Types {
			names = append(names, typeNames(inner, resolved)...)
		}
		return names
	case *ast.Intersection:
		var names []string
		for _, inner := range node.Types {
			names = append(names, typeNames(inner, resolved)...)
		}
		return names
	}
	// builtin Identifier type, e.g. string, int
	return nil
}

func equalFold(a, b string) bool {
	if len(a) != len(b) {
		return false
	}
	for i := range a {
		ca, cb := a[i], b[i]
		if 'A' <= ca && ca <= 'Z' {
			ca += 'a' - 'A'
		}
		if 'A' <= cb && cb <= 'Z' {
			cb += 'a' - 'A'
		}
		if ca != cb {
			return false
		}
	}
	return true
}
