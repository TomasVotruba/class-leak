# Class Leak - Go port

A Go reimplementation of the `class-leak check` command, built on
[php-parser-in-go](https://github.com/rectorphp/php-parser-in-go).

It finds classes that are declared but never referenced anywhere in the scanned
paths - the same job as the PHP `vendor/bin/class-leak check`.

## Build

From the repository root:

```bash
make build
```

This builds `bin/class-leak-go`. Or build directly:

```bash
cd go
go build -o class-leak ./cmd/class-leak
```

## Usage

```bash
bin/class-leak-go check src
```

Options (matching the PHP tool):

| Flag | Description |
| --- | --- |
| `--skip-type` | Class type to skip (repeatable) |
| `--skip-suffix` | Class name suffix to skip (repeatable) |
| `--skip-path` | Path or directory name to skip (repeatable) |
| `--skip-attribute` | Class attribute to skip (repeatable) |
| `--include-entities` | Include Doctrine ORM/ODM entities (skipped by default) |
| `--file-extension` | File extension to check (repeatable, default `php`) |
| `--json` | Output as JSON |

Exit code is `1` when unused classes are found, `0` otherwise.

The JSON output is byte-compatible with the PHP tool (the PHP text mode prints a
few stray leading blank lines before the JSON; the Go tool emits clean JSON).

## Scope and differences from the PHP tool

- Only the `check` command is ported. The `unused-public` PHPStan extension is
  not included; it is bound to the PHPStan runtime.
- **Trait grouping.** The PHP tool decides whether an unused class is a trait
  with the runtime `trait_exists()`, so it only groups a trait correctly when
  that trait is autoloadable. The Go port reads the declaration kind straight
  from the AST, so it is reliable regardless of autoloading. On a normally
  autoloaded project the two agree.
- **`is_a` type-skip.** The PHP tool resolves `--skip-type` inheritance through
  the target project's autoloader. The Go port builds the class hierarchy from
  the parsed files instead. An ancestor that lives only in an unscanned
  dependency (for example `vendor/`) still matches when its name appears
  directly in an `extends`/`implements` clause, but a chain that passes through
  such an unparsed intermediate class is not followed. Scanning an autoloaded
  project's own sources produces identical results.
- A file that fails to parse aborts the run, matching the PHP behavior; the
  error message text differs.

## Layout

```
cmd/class-leak      CLI entry point
internal/finder     file discovery + skip-path handling
internal/php        parsing, name resolution, class/used-name/ctor-param visitors
internal/graph      class inheritance graph (is_a) + glob matching
internal/filter     unused-class filtering + default skip lists
internal/report     grouping + text/JSON output
internal/runner     end-to-end pipeline
internal/model      value types
```
