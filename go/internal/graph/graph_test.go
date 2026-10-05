package graph

import "testing"

func TestGlobMatch(t *testing.T) {
	cases := []struct {
		pattern, s string
		want       bool
	}{
		{`App\*`, `App\Foo\Bar`, true},
		{`App\*`, `Other\Foo`, false},
		{`*Controller`, `App\HomeController`, true},
		{`*Controller`, `App\HomeService`, false},
		{`App\?oo`, `App\Foo`, true},
		{`App\?oo`, `App\Fooo`, false},
		{`Exact\Name`, `Exact\Name`, true},
		{`*`, `Anything\Here`, true},
	}
	for _, c := range cases {
		if got := globMatch(c.pattern, c.s); got != c.want {
			t.Errorf("globMatch(%q,%q)=%v want %v", c.pattern, c.s, got, c.want)
		}
	}
}

func TestIsAExactAndGlob(t *testing.T) {
	g := New()
	// empty registry: exact equality still holds, glob still works
	if !g.IsA("Foo\\Bar", "Foo\\Bar") {
		t.Error("exact equality should match")
	}
	if !g.IsA("App\\HomeController", "*Controller") {
		t.Error("glob should match")
	}
	if g.IsA("App\\Home", "App\\Other") {
		t.Error("unrelated should not match")
	}
}
