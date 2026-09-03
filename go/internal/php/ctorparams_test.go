package php

import (
	"reflect"
	"testing"
)

func TestResolveConstructorParamTypes(t *testing.T) {
	pf, err := Parse("../../../tests/ConstructorParamTypeResolver/Fixture/WithConstructorInjection.php")
	if err != nil {
		t.Fatal(err)
	}
	got := ResolveConstructorParamTypes(pf)
	src := "TomasVotruba\\ClassLeak\\Tests\\ConstructorParamTypeResolver\\Source\\"
	want := []string{src + "FirstInjectedInterface", src + "SecondInjectedInterface"}
	if !reflect.DeepEqual(got, want) {
		t.Fatalf("got %v want %v", got, want)
	}
}
