<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Tests\UseImportsResolver\Fixture;

use TomasVotruba\ClassLeak\Tests\UseImportsResolver\Source\FirstUsedClass;
use TomasVotruba\ClassLeak\Tests\UseImportsResolver\Source\SecondUsedClass;

final class FileUsingOtherClasses
{
    public function run(FirstUsedClass $firstUsedClass): SecondUsedClass
    {
        return new SecondUsedClass();
    }
}
