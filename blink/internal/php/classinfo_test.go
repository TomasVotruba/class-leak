package php

import (
	"reflect"
	"testing"
)

const fixtureNS = "TomasVotruba\\ClassLeak\\Tests\\ClassNameResolver\\Fixture\\"
const clFixture = "../../../tests/ClassNameResolver/Fixture/"

func TestResolveClassInfoSomeClass(t *testing.T) {
	pf, err := Parse(clFixture + "SomeClass.php")
	if err != nil {
		t.Fatal(err)
	}
	info, ok := ResolveClassInfo(pf)
	if !ok {
		t.Fatal("expected class found")
	}
	if info.ClassName != fixtureNS+"SomeClass" {
		t.Fatalf("class name: %q", info.ClassName)
	}
	if info.HasParentClassOrIface {
		t.Fatal("expected no parent")
	}
	want := []string{fixtureNS + "SomeAttribute", fixtureNS + "SomeMethodAttribute"}
	if !reflect.DeepEqual(info.Attributes, want) {
		t.Fatalf("attributes: %v", info.Attributes)
	}
}

func TestResolveClassInfoAnyComment(t *testing.T) {
	pf, _ := Parse(clFixture + "ClassWithAnyComment.php")
	info, ok := ResolveClassInfo(pf)
	if !ok {
		t.Fatal("expected class found")
	}
	if len(info.Attributes) != 0 {
		t.Fatalf("attributes: %v", info.Attributes)
	}
}

func TestResolveClassInfoApiComment(t *testing.T) {
	pf, _ := Parse(clFixture + "ClassWithApiComment.php")
	if _, ok := ResolveClassInfo(pf); ok {
		t.Fatal("expected @api class to be skipped")
	}
}
