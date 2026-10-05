package finder

import "testing"

const fixtureDir = "../../../tests/Finder/Fixture"

func TestFindPhpFiles(t *testing.T) {
	files, err := FindPhpFiles([]string{fixtureDir}, []string{"php", "phtml"}, nil)
	if err != nil {
		t.Fatal(err)
	}
	if len(files) != 4 {
		t.Fatalf("want 4, got %d: %v", len(files), files)
	}

	files, _ = FindPhpFiles([]string{fixtureDir}, []string{"php"}, nil)
	if len(files) != 3 {
		t.Fatalf("want 3, got %d: %v", len(files), files)
	}
}

func TestSkipByDirName(t *testing.T) {
	files, _ := FindPhpFiles([]string{fixtureDir}, []string{"php"}, []string{"more-nested"})
	if len(files) != 2 {
		t.Fatalf("want 2, got %d: %v", len(files), files)
	}
}

func TestSkipByRelativePath(t *testing.T) {
	files, _ := FindPhpFiles([]string{fixtureDir}, []string{"php"}, []string{fixtureDir + "/some/more-nested"})
	if len(files) != 2 {
		t.Fatalf("want 2, got %d: %v", len(files), files)
	}
}

func TestMissingPath(t *testing.T) {
	if _, err := FindPhpFiles([]string{"/does/not/exist"}, []string{"php"}, nil); err == nil {
		t.Fatal("expected error for missing path")
	}
}
