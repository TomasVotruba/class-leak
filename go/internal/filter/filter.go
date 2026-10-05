package filter

import (
	"strings"

	"github.com/tomasvotruba/class-leak/go/internal/graph"
	"github.com/tomasvotruba/class-leak/go/internal/model"
)

// Filter narrows declared classes down to the possibly-unused ones, applying
// the same skip rules as the PHP tool.
type Filter struct {
	graph *graph.Graph
}

func New(g *graph.Graph) *Filter {
	return &Filter{graph: g}
}

// Filter returns the classes that are declared but never referenced and not
// excluded by a skip rule, default type/attribute, entity/serialized marker,
// suffix, or constructor-injected interface.
func (f *Filter) Filter(
	filesWithClasses []model.FileWithClass,
	usedClassNames []string,
	typesToSkip []string,
	suffixesToSkip []string,
	attributesToSkip []string,
	includeEntities bool,
	constructorInjectedNames []string,
) []model.FileWithClass {
	used := toSet(usedClassNames)
	injected := toSet(constructorInjectedNames)

	types := append(append([]string{}, typesToSkip...), defaultTypesToSkip...)
	attributes := append(append([]string{}, attributesToSkip...), defaultAttributesToSkip...)

	var result []model.FileWithClass

	for _, fileWithClass := range filesWithClasses {
		if used[fileWithClass.ClassName] {
			continue
		}
		if f.shouldSkip(fileWithClass.ClassName, types) {
			continue
		}
		if !includeEntities && fileWithClass.IsEntity {
			continue
		}
		if fileWithClass.IsSerialized {
			continue
		}
		if isInterfaceConstructorInjected(fileWithClass, injected) {
			continue
		}
		if hasSkippedSuffix(fileWithClass.ClassName, suffixesToSkip) {
			continue
		}
		if f.hasSkippedAttribute(fileWithClass.Attributes, attributes) {
			continue
		}
		result = append(result, fileWithClass)
	}

	return result
}

func (f *Filter) shouldSkip(typeName string, skips []string) bool {
	for _, skip := range skips {
		if f.graph.IsA(typeName, skip) {
			return true
		}
	}
	return false
}

func (f *Filter) hasSkippedAttribute(attributes, skips []string) bool {
	for _, attribute := range attributes {
		if f.shouldSkip(attribute, skips) {
			return true
		}
	}
	return false
}

func hasSkippedSuffix(className string, suffixes []string) bool {
	for _, suffix := range suffixes {
		if strings.HasSuffix(className, suffix) {
			return true
		}
	}
	return false
}

func isInterfaceConstructorInjected(fileWithClass model.FileWithClass, injected map[string]bool) bool {
	for _, interfaceName := range fileWithClass.InterfaceNames {
		if injected[interfaceName] {
			return true
		}
	}
	return false
}

func toSet(values []string) map[string]bool {
	set := make(map[string]bool, len(values))
	for _, v := range values {
		set[v] = true
	}
	return set
}
