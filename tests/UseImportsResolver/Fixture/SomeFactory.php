<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Tests\UseImportsResolver\Fixture;

use TomasVotruba\ClassLeak\Tests\UseImportsResolver\Source\FourthUsedClass;

final class SomeFactory
{
    public static function create(): FourthUsedClass
    {
        return new FourthUsedClass();
    }
}
