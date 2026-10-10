# Class Leak

[![Downloads total](https://img.shields.io/packagist/dt/tomasvotruba/class-leak.svg?style=flat-square)](https://packagist.org/packages/tomasvotruba/class-leak/stats)

Find leaking classes that you never use... and get rid of them.

## Install

```bash
composer require tomasvotruba/class-leak --dev
```

## Usage

Pass directories you want to check:

```bash
vendor/bin/class-leak check src
```

Make sure to exclude `/tests` directories, to keep reporting classes that are used in tests, but never used in the code-base.

<br>

Many types are excluded by default, as they're collected by framework magic, e.g. console command classes.

<br>

## Exclude what you use

Do you want to skip classes of certain type?

```bash
vendor/bin/class-leak check src --skip-type="App\\Contract\\SomeInterface"
```

<br>

Classes ending with `Controller`, `Test` or `TestCase` are skipped by default, as they are called by the router or the test runner. What if your other classes do no implement any type?

```bash
vendor/bin/class-leak check src --skip-suffix="Handler"
```

<br>

Do you want to skip classes using a specific attribute?

```bash
vendor/bin/class-leak check src --skip-attribute="App\\Attribute\\AsController"
```

<br>

## Fast mode (Go port, experimental)

A Go port of the same checks is included as a proof of concept. Enable it with `--blink`:

```bash
vendor/bin/class-leak check src --blink
```

It reuses the same options and output. The `bin/class-leak-go` launcher builds the binary on first run, so a Go toolchain is required.

<br>

Happy coding!
