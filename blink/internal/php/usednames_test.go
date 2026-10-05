package php

import (
	"reflect"
	"testing"
)

const uiFixture = "../../../tests/UseImportsResolver/Fixture/"
const uiSourceNS = "TomasVotruba\\ClassLeak\\Tests\\UseImportsResolver\\Source\\"
const uiFixtureNS = "TomasVotruba\\ClassLeak\\Tests\\UseImportsResolver\\Fixture\\"

func TestResolveUsedNames(t *testing.T) {
	cases := []struct {
		file string
		want []string
	}{
		{"FileUsingOtherClasses.php", []string{uiSourceNS + "FirstUsedClass", uiSourceNS + "SecondUsedClass"}},
		{"FileUsesStaticCall.php", []string{uiFixtureNS + "SomeFactory", uiSourceNS + "FourthUsedClass"}},
	}
	for _, c := range cases {
		pf, err := Parse(uiFixture + c.file)
		if err != nil {
			t.Fatalf("%s: %v", c.file, err)
		}
		got := ResolveUsedNames(pf)
		if !reflect.DeepEqual(got, c.want) {
			t.Fatalf("%s: got %v want %v", c.file, got, c.want)
		}
	}
}

func TestParseErrorReturnsError(t *testing.T) {
	if _, err := Parse(uiFixture + "ParseError.php"); err == nil {
		t.Fatal("expected parse error")
	}
}
