package graph

// globMatch matches a string against an fnmatch-style pattern supporting `*`
// (any run of characters, including none) and `?` (a single character). Other
// characters match literally. Unlike path.Match, no character is treated as a
// separator, so `*` spans namespace backslashes.
func globMatch(pattern, s string) bool {
	// iterative backtracking match
	var pi, si int
	star, mark := -1, 0

	for si < len(s) {
		if pi < len(pattern) && (pattern[pi] == '?' || pattern[pi] == s[si]) {
			pi++
			si++
			continue
		}
		if pi < len(pattern) && pattern[pi] == '*' {
			star = pi
			mark = si
			pi++
			continue
		}
		if star != -1 {
			pi = star + 1
			mark++
			si = mark
			continue
		}
		return false
	}

	for pi < len(pattern) && pattern[pi] == '*' {
		pi++
	}
	return pi == len(pattern)
}
