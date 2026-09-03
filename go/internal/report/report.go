package report

import (
	"bytes"
	"encoding/json"
	"fmt"
	"io"
	"os"
	"path/filepath"

	"github.com/tomasvotruba/class-leak/go/internal/model"
)

// Group splits unused classes into with-parent, parentless, and trait buckets,
// matching UnusedClassesResultFactory.
func Group(unused []model.FileWithClass) model.UnusedClassesResult {
	result := model.UnusedClassesResult{
		ParentLess:  []model.FileWithClass{},
		WithParents: []model.FileWithClass{},
		Traits:      []model.FileWithClass{},
	}
	for _, fileWithClass := range unused {
		switch {
		case fileWithClass.HasParentClassOrIface:
			result.WithParents = append(result.WithParents, fileWithClass)
		case fileWithClass.IsTrait():
			result.Traits = append(result.Traits, fileWithClass)
		default:
			result.ParentLess = append(result.ParentLess, fileWithClass)
		}
	}
	return result
}

type jsonResult struct {
	UnusedClassCount        int                   `json:"unused_class_count"`
	UnusedParentLessClasses []model.FileWithClass `json:"unused_parent_less_classes"`
	UnusedClassesWithParent []model.FileWithClass `json:"unused_classes_with_parents"`
	UnusedTraits            []model.FileWithClass `json:"unused_traits"`
}

// JSON renders the result as pretty JSON matching the PHP tool: 4-space indent,
// unescaped slashes, no HTML escaping.
func JSON(result model.UnusedClassesResult) (string, error) {
	payload := jsonResult{
		UnusedClassCount:        result.Count(),
		UnusedParentLessClasses: result.ParentLess,
		UnusedClassesWithParent: result.WithParents,
		UnusedTraits:            result.Traits,
	}

	var buf bytes.Buffer
	encoder := json.NewEncoder(&buf)
	encoder.SetEscapeHTML(false)
	encoder.SetIndent("", "    ")
	if err := encoder.Encode(payload); err != nil {
		return "", err
	}
	// Encoder appends a trailing newline; drop it to match json_encode output.
	return string(bytes.TrimRight(buf.Bytes(), "\n")), nil
}

// Report writes the result and returns the process exit code (0 clean, 1 when
// unused classes were found).
func Report(out io.Writer, result model.UnusedClassesResult, asJSON bool) (int, error) {
	if asJSON {
		encoded, err := JSON(result)
		if err != nil {
			return 1, err
		}
		fmt.Fprintln(out, encoded)
		return 0, nil
	}

	if result.Count() == 0 {
		fmt.Fprintln(out, "\n [OK] All services are used. Great job!")
		return 0, nil
	}

	printGroup(out, "Classes with a parent/interface", result.WithParents)
	printGroup(out, "Classes without any parent/interface - easier to remove", result.ParentLess)
	printGroup(out, "Unused traits - the easiest to remove", result.Traits)

	fmt.Fprintf(out, "\n [ERROR] Found %d unused classes.\n"+
		" Remove them or skip them using \"--skip-type\" option\n", result.Count())
	return 1, nil
}

func printGroup(out io.Writer, title string, files []model.FileWithClass) {
	if len(files) == 0 {
		return
	}
	fmt.Fprintf(out, "\n%s:\n", title)
	for _, fileWithClass := range files {
		fmt.Fprintln(out, relativePath(fileWithClass.FilePath))
	}
}

func relativePath(path string) string {
	cwd, err := os.Getwd()
	if err != nil {
		return path
	}
	rel, err := filepath.Rel(cwd, path)
	if err != nil {
		return path
	}
	return rel
}
