<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Tests\UseImportsResolver\Fixture;

use TomasVotruba\ClassLeak\Tests\UseImportsResolver\Source\FourthUsedClass;

final class FileUsesStaticCall
{
    public function other() : FourthUsedClass
    {
        return SomeFactory::create();
    }
}
