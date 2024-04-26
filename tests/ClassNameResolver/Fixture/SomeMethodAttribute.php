<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Tests\ClassNameResolver\Fixture;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class SomeMethodAttribute
{
}
