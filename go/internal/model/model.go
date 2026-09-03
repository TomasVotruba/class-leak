package model

import "encoding/json"

// ClassKind marks how a class-like was declared.
type ClassKind int

const (
	KindClass ClassKind = iota
	KindInterface
	KindTrait
	KindEnum
)

// ClassNames holds what the class-name resolver extracts from a single file.
type ClassNames struct {
	ClassName             string
	HasParentClassOrIface bool
	Attributes            []string
	InterfaceNames        []string
	Kind                  ClassKind
}

// FileWithClass is one declared class tied to its file.
type FileWithClass struct {
	FilePath              string
	ClassName             string
	HasParentClassOrIface bool
	Attributes            []string
	InterfaceNames        []string
	Kind                  ClassKind
	IsEntity              bool
	IsSerialized          bool
}

func (f FileWithClass) IsTrait() bool { return f.Kind == KindTrait }

// jsonFileWithClass matches the PHP JsonSerializable shape.
type jsonFileWithClass struct {
	FilePath   string   `json:"file_path"`
	Class      string   `json:"class"`
	Attributes []string `json:"attributes"`
}

func (f FileWithClass) MarshalJSON() ([]byte, error) {
	attrs := f.Attributes
	if attrs == nil {
		attrs = []string{}
	}
	return json.Marshal(jsonFileWithClass{
		FilePath:   f.FilePath,
		Class:      f.ClassName,
		Attributes: attrs,
	})
}

// UnusedClassesResult groups unused classes for reporting.
type UnusedClassesResult struct {
	ParentLess  []FileWithClass
	WithParents []FileWithClass
	Traits      []FileWithClass
}

func (r UnusedClassesResult) Count() int {
	return len(r.ParentLess) + len(r.WithParents) + len(r.Traits)
}
