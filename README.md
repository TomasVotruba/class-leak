# Class Leak

[![Downloads total](https://img.shields.io/packagist/dt/tomasvotruba/class-leak.svg?style=flat-square)](https://packagist.org/packages/tomasvotruba/class-leak/stats)

Find leaking classes that you never use... and get rid of them.

## Install

```bash
composer require tomasvotruba/class-leak --dev
```

## Usage

How to avoid it? Add check to your CI:

```bash
vendor/bin/class-leak bin src
```

Make sure to exclude `/tests` directories, to keep reporting classes that are used in tests, but never used in the code-base.

<br>

Many types are excluded by default, as they're collected by framework magic, e.g. console command classes. To exlude another class, e.g. your interface collector, use `--skip-type`:

```bash
vendor/bin/class-leak check bin src --skip-type="App\\Contract\\SomeInterface"
```
