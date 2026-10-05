package main

import (
	"flag"
	"fmt"
	"os"
	"strings"

	"github.com/tomasvotruba/class-leak/go/internal/report"
	"github.com/tomasvotruba/class-leak/go/internal/runner"
)

// stringList is a repeatable string flag.
type stringList []string

func (s *stringList) String() string { return strings.Join(*s, ",") }
func (s *stringList) Set(v string) error {
	*s = append(*s, v)
	return nil
}

func main() {
	if len(os.Args) < 2 || os.Args[1] != "check" {
		fmt.Fprintln(os.Stderr, "usage: class-leak check <paths...> [options]")
		os.Exit(2)
	}

	exitCode, err := runCheck(os.Args[2:])
	if err != nil {
		fmt.Fprintln(os.Stderr, "Error: "+err.Error())
		os.Exit(1)
	}
	os.Exit(exitCode)
}

func runCheck(args []string) (int, error) {
	var skipType, skipSuffix, skipPath, skipAttribute, fileExtension stringList
	var includeEntities, asJSON, ansi bool

	fs := flag.NewFlagSet("check", flag.ContinueOnError)
	fs.Var(&skipType, "skip-type", "class type to skip (repeatable)")
	fs.Var(&skipSuffix, "skip-suffix", "class suffix to skip (repeatable)")
	fs.Var(&skipPath, "skip-path", "path or directory name to skip (repeatable)")
	fs.Var(&skipAttribute, "skip-attribute", "class attribute to skip (repeatable)")
	fs.Var(&fileExtension, "file-extension", "file extension to check (repeatable, default php)")
	fs.BoolVar(&includeEntities, "include-entities", false, "include Doctrine entities")
	fs.BoolVar(&asJSON, "json", false, "output as JSON")
	fs.BoolVar(&ansi, "ansi", false, "kept for backward compatibility")

	// allow flags and positional paths in any order (Symfony-style)
	var paths []string
	for len(args) > 0 {
		if err := fs.Parse(args); err != nil {
			return 2, err
		}
		rest := fs.Args()
		if len(rest) == 0 {
			break
		}
		paths = append(paths, rest[0])
		args = rest[1:]
	}

	if len(paths) == 0 {
		return 2, fmt.Errorf("no paths given")
	}

	result, err := runner.Run(runner.Options{
		Paths:           paths,
		FileExtensions:  fileExtension,
		SkipType:        skipType,
		SkipSuffix:      skipSuffix,
		SkipPath:        skipPath,
		SkipAttribute:   skipAttribute,
		IncludeEntities: includeEntities,
	})
	if err != nil {
		return 1, err
	}

	return report.Report(os.Stdout, result, asJSON)
}
