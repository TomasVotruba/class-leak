package finder

import (
	"fmt"
	"io/fs"
	"os"
	"path/filepath"
	"sort"
	"strings"
)

// FindPhpFiles walks paths for files matching extensions, honoring skip-path
// entries. A skip entry without a separator is treated as a directory name
// excluded at any depth; otherwise it is a real path prefix to exclude.
// Returned paths are resolved real paths, sorted by name.
func FindPhpFiles(paths, extensions, pathsToSkip []string) ([]string, error) {
	for _, p := range paths {
		if _, err := os.Stat(p); err != nil {
			return nil, fmt.Errorf("path does not exist: %q", p)
		}
	}

	excludedDirNames := map[string]bool{}
	var excludedRealPaths []string
	for _, skip := range pathsToSkip {
		if !strings.ContainsAny(skip, "/\\") {
			excludedDirNames[skip] = true
			continue
		}
		abs, err := filepath.Abs(skip)
		if err != nil {
			continue
		}
		if real, err := filepath.EvalSymlinks(abs); err == nil {
			excludedRealPaths = append(excludedRealPaths, real)
		}
	}

	seen := map[string]bool{}
	var filePaths []string

	for _, root := range paths {
		err := filepath.WalkDir(root, func(path string, d fs.DirEntry, err error) error {
			if err != nil {
				return err
			}
			if d.IsDir() {
				if excludedDirNames[d.Name()] {
					return filepath.SkipDir
				}
				return nil
			}
			if !hasExtension(path, extensions) && !hasPhpShebang(path) {
				return nil
			}
			abs, err := filepath.Abs(path)
			if err != nil {
				return nil
			}
			real, err := filepath.EvalSymlinks(abs)
			if err != nil {
				return nil
			}
			if isWithinExcluded(real, excludedRealPaths) {
				return nil
			}
			if !seen[real] {
				seen[real] = true
				filePaths = append(filePaths, real)
			}
			return nil
		})
		if err != nil {
			return nil, err
		}
	}

	sort.Strings(filePaths)
	return filePaths, nil
}

func hasExtension(path string, extensions []string) bool {
	ext := strings.TrimPrefix(filepath.Ext(path), ".")
	for _, want := range extensions {
		if ext == want {
			return true
		}
	}
	return false
}

// hasPhpShebang detects extensionless executables like "bin/console" whose
// first line is a PHP shebang (e.g. #!/usr/bin/env php).
func hasPhpShebang(path string) bool {
	if filepath.Ext(path) != "" {
		return false
	}
	f, err := os.Open(path)
	if err != nil {
		return false
	}
	defer f.Close()

	buf := make([]byte, 128)
	n, _ := f.Read(buf)
	firstLine := string(buf[:n])
	if idx := strings.IndexByte(firstLine, '\n'); idx >= 0 {
		firstLine = firstLine[:idx]
	}
	return strings.HasPrefix(strings.TrimSpace(firstLine), "#!") && strings.Contains(firstLine, "php")
}

func isWithinExcluded(realPath string, excludedRealPaths []string) bool {
	for _, excluded := range excludedRealPaths {
		if realPath == excluded || strings.HasPrefix(realPath, excluded+string(filepath.Separator)) {
			return true
		}
	}
	return false
}
