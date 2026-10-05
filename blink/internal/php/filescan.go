package php

import (
	"os"
	"strings"
)

// entityMarkers are raw substrings hinting a Doctrine ORM/ODM entity.
var entityMarkers = []string{
	"Doctrine\\ODM\\MongoDB\\Mapping\\Annotations",
	"Doctrine\\ORM\\Annotations",
	"@ORM\\Entity",
	"@Entity",
	"@ODM\\Document",
	"@Document",
}

// ScanFileFlags reports the raw-content flags the PHP tool derives by string
// search: whether the file looks like a serialized class or a Doctrine entity.
func ScanFileFlags(filePath string) (isSerialized, isEntity bool) {
	content, err := os.ReadFile(filePath)
	if err != nil {
		return false, false
	}
	text := string(content)

	isSerialized = strings.Contains(text, "@Serializer")
	for _, marker := range entityMarkers {
		if strings.Contains(text, marker) {
			isEntity = true
			break
		}
	}
	return isSerialized, isEntity
}
