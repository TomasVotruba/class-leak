# Class Leak

[![Downloads total](https://img.shields.io/packagist/dt/tomas-votruba/class-leak.svg?style=flat-square)](https://packagist.org/packages/tomas-votruba/class-leak/stats)

Find leaking classes that you never use... and get rif of them.

## Install

```bash
composer require tomas-votruba/class-leak --dev
```

## Usage

How to avoid it? Add check to your CI:

```bash
vendor/bin/class-leak .
```
