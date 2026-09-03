package runner

import (
	"github.com/tomasvotruba/class-leak/go/internal/filter"
	"github.com/tomasvotruba/class-leak/go/internal/finder"
	"github.com/tomasvotruba/class-leak/go/internal/graph"
	"github.com/tomasvotruba/class-leak/go/internal/model"
	"github.com/tomasvotruba/class-leak/go/internal/php"
	"github.com/tomasvotruba/class-leak/go/internal/report"
)

// Options mirrors the check command flags.
type Options struct {
	Paths           []string
	FileExtensions  []string
	SkipType        []string
	SkipSuffix      []string
	SkipPath        []string
	SkipAttribute   []string
	IncludeEntities bool
}

// Run executes the full unused-class analysis and returns the grouped result.
func Run(opts Options) (model.UnusedClassesResult, error) {
	extensions := opts.FileExtensions
	if len(extensions) == 0 {
		extensions = []string{"php"}
	}

	allFilePaths, err := finder.FindPhpFiles(opts.Paths, extensions, nil)
	if err != nil {
		return model.UnusedClassesResult{}, err
	}
	checkFilePaths, err := finder.FindPhpFiles(opts.Paths, extensions, opts.SkipPath)
	if err != nil {
		return model.UnusedClassesResult{}, err
	}

	usedNames := map[string]bool{}
	injectedNames := map[string]bool{}
	classGraph := graph.New()
	parsedByPath := make(map[string]*php.ParsedFile, len(allFilePaths))

	for _, path := range allFilePaths {
		pf, err := php.Parse(path)
		if err != nil {
			return model.UnusedClassesResult{}, err
		}
		if pf == nil {
			continue
		}
		parsedByPath[path] = pf
		classGraph.Add(pf)
		for _, name := range php.ResolveUsedNames(pf) {
			usedNames[name] = true
		}
		for _, name := range php.ResolveConstructorParamTypes(pf) {
			injectedNames[name] = true
		}
	}

	var filesWithClasses []model.FileWithClass
	for _, path := range checkFilePaths {
		pf := parsedByPath[path]
		if pf == nil {
			continue
		}
		info, ok := php.ResolveClassInfo(pf)
		if !ok {
			continue
		}
		isSerialized, isEntity := php.ScanFileFlags(path)
		filesWithClasses = append(filesWithClasses, model.FileWithClass{
			FilePath:              path,
			ClassName:             info.ClassName,
			HasParentClassOrIface: info.HasParentClassOrIface,
			Attributes:            info.Attributes,
			InterfaceNames:        info.InterfaceNames,
			Kind:                  info.Kind,
			IsEntity:              isEntity,
			IsSerialized:          isSerialized,
		})
	}

	unused := filter.New(classGraph).Filter(
		filesWithClasses,
		setToSlice(usedNames),
		opts.SkipType,
		opts.SkipSuffix,
		opts.SkipAttribute,
		opts.IncludeEntities,
		setToSlice(injectedNames),
	)

	return report.Group(unused), nil
}

func setToSlice(set map[string]bool) []string {
	out := make([]string, 0, len(set))
	for v := range set {
		out = append(out, v)
	}
	return out
}
