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

What if your classes do no implement any type?

```bash
vendor/bin/class-leak check src --skip-suffix="Controller"
```

<br>

Do you want to skip classes using a specific attribute?

```bash
vendor/bin/class-leak check src --skip-attribute="App\\Attribute\\AsController"
```

<br>

## Find Unused Public Elements

It's easy to find unused private class elements, because they're not used in the class itself. But what about public methods, properties and constants?

This package also ships a PHPStan extension that detects them:

* find a public element
* find all its uses in code and templates
* if none is found, it's probably unused

Include the extension in your `phpstan.neon`:

```yaml
# phpstan.neon
includes:
    - vendor/tomasvotruba/class-leak/packages/unused-public/config/extension.neon
```

Enable each check on its own:

```yaml
# phpstan.neon
parameters:
    unused_public:
        methods: true
        properties: true
        constants: true
```

<br>

Too many reported methods to handle at once? Set a maximum allowed % instead:

```yaml
# phpstan.neon
parameters:
    unused_public:
        methods: 2.5
```

Maximum 2.5 % of all public methods is allowed as unused - above alerts, below is tolerated.

<br>

Want to detect local-only calls that should become `private`/`protected` instead of being removed?

```yaml
# phpstan.neon
parameters:
    unused_public:
        local_methods: true
```

<br>

Some methods are used only in TWIG or Blade templates. Add their directories to skip false positives:

```yaml
# phpstan.neon
parameters:
    unused_public:
        template_paths:
            - templates
```

<br>

Is an element public on purpose, as designed API? Mark the class or element with `@api` to skip it:

```php
final class Book
{
    /**
     * @api
     */
    public function getName(): string
    {
        return $this->name;
    }
}
```

<br>

Happy coding!
