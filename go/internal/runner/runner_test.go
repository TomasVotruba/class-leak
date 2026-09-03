package runner

import (
	"sort"
	"testing"

	"github.com/tomasvotruba/class-leak/go/internal/model"
)

func names(files []model.FileWithClass) []string {
	out := make([]string, 0, len(files))
	for _, f := range files {
		out = append(out, f.ClassName)
	}
	sort.Strings(out)
	return out
}

func equal(a, b []string) bool {
	if len(a) != len(b) {
		return false
	}
	for i := range a {
		if a[i] != b[i] {
			return false
		}
	}
	return true
}

func TestRunGroupsUnusedClasses(t *testing.T) {
	result, err := Run(Options{Paths: []string{"testdata/src"}})
	if err != nil {
		t.Fatal(err)
	}

	// UsedService is injected into Consumer's constructor, so it is used.
	// OrphanClass is referenced by OrphanWithParent's extends, so it is used too.
	// Unused: Consumer (parentless), OrphanWithParent (has parent), OrphanTrait.
	if got := names(result.ParentLess); !equal(got, []string{"Demo\\Consumer"}) {
		t.Errorf("parentless: %v", got)
	}
	if got := names(result.WithParents); !equal(got, []string{"Demo\\OrphanWithParent"}) {
		t.Errorf("with-parents: %v", got)
	}
	if got := names(result.Traits); !equal(got, []string{"Demo\\OrphanTrait"}) {
		t.Errorf("traits: %v", got)
	}
	if result.Count() != 3 {
		t.Errorf("count: %d", result.Count())
	}
}

func TestRunSkipSuffix(t *testing.T) {
	result, err := Run(Options{Paths: []string{"testdata/src"}, SkipSuffix: []string{"Class"}})
	if err != nil {
		t.Fatal(err)
	}
	for _, f := range result.ParentLess {
		if f.ClassName == "Demo\\OrphanClass" {
			t.Error("OrphanClass should be skipped by suffix")
		}
	}
}

func TestRunSkipTypeSkipsSubclass(t *testing.T) {
	result, err := Run(Options{Paths: []string{"testdata/src"}, SkipType: []string{"Demo\\OrphanClass"}})
	if err != nil {
		t.Fatal(err)
	}
	all := append(append([]model.FileWithClass{}, result.ParentLess...), result.WithParents...)
	for _, f := range all {
		if f.ClassName == "Demo\\OrphanClass" || f.ClassName == "Demo\\OrphanWithParent" {
			t.Errorf("%s should be skipped via type inheritance", f.ClassName)
		}
	}
}
