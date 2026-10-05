package filter

import (
	"testing"

	"github.com/tomasvotruba/class-leak/go/internal/graph"
	"github.com/tomasvotruba/class-leak/go/internal/model"
)

const filterNS = "TomasVotruba\\ClassLeak\\Tests\\Filtering\\Fixture\\"

func syncJudge() model.FileWithClass {
	return model.FileWithClass{
		FilePath:              "../../../tests/Filtering/Fixture/SyncJudge.php",
		ClassName:             filterNS + "SyncJudge",
		HasParentClassOrIface: true,
		InterfaceNames:        []string{filterNS + "SyncJudgeInterface"},
	}
}

func TestSkipsClassWhoseInterfaceIsConstructorInjected(t *testing.T) {
	f := New(graph.New())
	got := f.Filter(
		[]model.FileWithClass{syncJudge()},
		nil, nil, nil, nil, false,
		[]string{filterNS + "SyncJudgeInterface"},
	)
	if len(got) != 0 {
		t.Fatalf("expected empty, got %v", got)
	}
}

func TestKeepsClassWhoseInterfaceIsNotConstructorInjected(t *testing.T) {
	f := New(graph.New())
	got := f.Filter(
		[]model.FileWithClass{syncJudge()},
		nil, nil, nil, nil, false, nil,
	)
	if len(got) != 1 {
		t.Fatalf("expected 1 kept, got %v", got)
	}
}

func TestSkipsByDefaultType(t *testing.T) {
	f := New(graph.New())
	fwc := model.FileWithClass{ClassName: "Symfony\\Component\\Console\\Command\\Command"}
	got := f.Filter([]model.FileWithClass{fwc}, nil, nil, nil, nil, false, nil)
	if len(got) != 0 {
		t.Fatalf("default type should be skipped, got %v", got)
	}
}

func TestSkipsBySuffix(t *testing.T) {
	f := New(graph.New())
	fwc := model.FileWithClass{ClassName: "App\\HomeController"}
	got := f.Filter([]model.FileWithClass{fwc}, nil, nil, []string{"Controller"}, nil, false, nil)
	if len(got) != 0 {
		t.Fatalf("suffix should be skipped, got %v", got)
	}
}

func TestSkipsByUsedName(t *testing.T) {
	f := New(graph.New())
	fwc := model.FileWithClass{ClassName: "App\\Used"}
	got := f.Filter([]model.FileWithClass{fwc}, []string{"App\\Used"}, nil, nil, nil, false, nil)
	if len(got) != 0 {
		t.Fatalf("used name should be skipped, got %v", got)
	}
}
