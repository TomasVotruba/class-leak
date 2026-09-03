package php

import (
	"fmt"
	"os"

	"github.com/rectorphp/php-parser-in-go/pkg/ast"
	"github.com/rectorphp/php-parser-in-go/pkg/conf"
	"github.com/rectorphp/php-parser-in-go/pkg/errors"
	"github.com/rectorphp/php-parser-in-go/pkg/parser"
	"github.com/rectorphp/php-parser-in-go/pkg/version"
	"github.com/rectorphp/php-parser-in-go/pkg/visitor/classresolver"
)

const phpVersion = "8.4"

// ParsedFile is a parsed file with its resolved name map.
type ParsedFile struct {
	Root     ast.Vertex
	Resolved map[ast.Vertex]string
}

// Parse reads and parses a PHP file, returning its AST root and resolved names.
// A file that fails to parse returns a nil root without error, matching the PHP
// tool which skips unparseable files.
func Parse(filePath string) (*ParsedFile, error) {
	src, err := os.ReadFile(filePath)
	if err != nil {
		return nil, err
	}

	ver, err := version.New(phpVersion)
	if err != nil {
		return nil, err
	}

	var parseErr *errors.Error
	root, err := parser.Parse(src, conf.Config{
		Version: ver,
		ErrorHandlerFunc: func(e *errors.Error) {
			if parseErr == nil {
				parseErr = e
			}
		},
	})
	if err != nil {
		return nil, fmt.Errorf("could not parse file %q: %w", filePath, err)
	}
	if parseErr != nil {
		return nil, fmt.Errorf("could not parse file %q: %s", filePath, parseErr.Msg)
	}
	if root == nil {
		return nil, nil
	}

	return &ParsedFile{
		Root:     root,
		Resolved: classresolver.ResolveNames(root),
	}, nil
}
