package php

import (
	"strings"

	"github.com/rectorphp/php-parser-in-go/pkg/ast"
)

// nameResolver resolves short/relative class names to FQN using a file's
// namespace and use-import aliases. It fills the gap left by the upstream
// namespace resolver, which does not resolve attribute names.
type nameResolver struct {
	namespace string
	aliases   map[string]string // short alias -> FQN
}

func newNameResolver(root ast.Vertex) *nameResolver {
	nr := &nameResolver{aliases: map[string]string{}}

	r, ok := root.(*ast.Root)
	if !ok {
		return nr
	}

	stmts := r.Stmts
	for _, s := range r.Stmts {
		if ns, ok := s.(*ast.StmtNamespace); ok {
			if ns.Name != nil {
				nr.namespace = nameParts(ns.Name)
			}
			stmts = append(stmts, ns.Stmts...)
		}
	}

	for _, s := range stmts {
		useList, ok := s.(*ast.StmtUseList)
		if !ok {
			continue
		}
		// only class-type use imports (skip function/const)
		if useList.Type != nil {
			continue
		}
		for _, u := range useList.Uses {
			use, ok := u.(*ast.StmtUse)
			if !ok || use.Use == nil {
				continue
			}
			fqn := nameParts(use.Use)
			alias := lastPart(fqn)
			if use.Alias != nil {
				if id, ok := use.Alias.(*ast.Identifier); ok {
					alias = string(id.Value)
				}
			}
			nr.aliases[alias] = fqn
		}
	}

	return nr
}

func (nr *nameResolver) resolve(name ast.Vertex) string {
	switch name.(type) {
	case *ast.NameFullyQualified:
		return nameParts(name)
	}

	full := nameParts(name)
	if full == "" {
		return ""
	}
	parts := strings.Split(full, "\\")
	if fqn, ok := nr.aliases[parts[0]]; ok {
		if len(parts) == 1 {
			return fqn
		}
		return fqn + "\\" + strings.Join(parts[1:], "\\")
	}
	if nr.namespace != "" {
		return nr.namespace + "\\" + full
	}
	return full
}

// nameParts joins a name node's parts into a backslash-separated string.
func nameParts(node ast.Vertex) string {
	var parts []ast.Vertex
	switch n := node.(type) {
	case *ast.Name:
		parts = n.Parts
	case *ast.NameFullyQualified:
		parts = n.Parts
	case *ast.NameRelative:
		parts = n.Parts
	default:
		return ""
	}

	var out []string
	for _, p := range parts {
		if np, ok := p.(*ast.NamePart); ok {
			out = append(out, string(np.Value))
		}
	}
	return strings.Join(out, "\\")
}

func lastPart(fqn string) string {
	idx := strings.LastIndex(fqn, "\\")
	if idx == -1 {
		return fqn
	}
	return fqn[idx+1:]
}
