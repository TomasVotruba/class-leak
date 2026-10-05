package report

import (
	"strings"
	"testing"

	"github.com/tomasvotruba/class-leak/go/internal/model"
)

func TestGroup(t *testing.T) {
	files := []model.FileWithClass{
		{ClassName: "A", HasParentClassOrIface: true},
		{ClassName: "B"},
		{ClassName: "C", Kind: model.KindTrait},
	}
	result := Group(files)
	if len(result.WithParents) != 1 || result.WithParents[0].ClassName != "A" {
		t.Errorf("with-parents: %v", result.WithParents)
	}
	if len(result.ParentLess) != 1 || result.ParentLess[0].ClassName != "B" {
		t.Errorf("parentless: %v", result.ParentLess)
	}
	if len(result.Traits) != 1 || result.Traits[0].ClassName != "C" {
		t.Errorf("traits: %v", result.Traits)
	}
	if result.Count() != 3 {
		t.Errorf("count: %d", result.Count())
	}
}

func TestJSONShape(t *testing.T) {
	result := Group([]model.FileWithClass{
		{FilePath: "/abs/src/Foo.php", ClassName: "App\\Foo", Attributes: []string{"App\\Attr"}},
	})
	got, err := JSON(result)
	if err != nil {
		t.Fatal(err)
	}
	// class name backslashes escaped, slashes unescaped, 4-space indent
	for _, want := range []string{
		`"unused_class_count": 1`,
		`"file_path": "/abs/src/Foo.php"`,
		`"class": "App\\Foo"`,
		`"unused_traits": []`,
	} {
		if !strings.Contains(got, want) {
			t.Errorf("missing %q in:\n%s", want, got)
		}
	}
	if strings.Contains(got, `\/`) {
		t.Errorf("slashes should be unescaped:\n%s", got)
	}
}

func TestJSONEmpty(t *testing.T) {
	got, _ := JSON(Group(nil))
	for _, want := range []string{
		`"unused_class_count": 0`,
		`"unused_parent_less_classes": []`,
		`"unused_classes_with_parents": []`,
		`"unused_traits": []`,
	} {
		if !strings.Contains(got, want) {
			t.Errorf("missing %q in:\n%s", want, got)
		}
	}
}
