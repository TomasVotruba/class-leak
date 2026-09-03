package graph

import (
	"github.com/rectorphp/php-parser-in-go/pkg/visitor/classresolver"
	"github.com/tomasvotruba/class-leak/go/internal/php"
)

// Graph resolves class inheritance across parsed files, backing the is_a style
// type-skip check. It also matches glob patterns against class names.
type Graph struct {
	registry *classresolver.Registry
}

func New() *Graph {
	return &Graph{registry: classresolver.NewRegistry()}
}

// Add records every class declared in a parsed file into the hierarchy.
func (g *Graph) Add(pf *php.ParsedFile) {
	g.registry.CollectWithResolvedNames(pf.Root, pf.Resolved)
}

// IsA reports whether typeName equals, extends, or implements skip, or matches
// it as a glob pattern - mirroring the PHP shouldSkip check.
func (g *Graph) IsA(typeName, skip string) bool {
	if g.registry.IsSubtypeOf(typeName, skip) {
		return true
	}
	return globMatch(skip, typeName)
}
