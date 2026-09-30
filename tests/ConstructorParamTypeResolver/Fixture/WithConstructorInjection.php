<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Tests\ConstructorParamTypeResolver\Fixture;

use TomasVotruba\ClassLeak\Tests\ConstructorParamTypeResolver\Source\FirstInjectedInterface;
use TomasVotruba\ClassLeak\Tests\ConstructorParamTypeResolver\Source\NotInjectedInterface;
use TomasVotruba\ClassLeak\Tests\ConstructorParamTypeResolver\Source\SecondInjectedInterface;

final class WithConstructorInjection
{
    public function __construct(FirstInjectedInterface $first, ?SecondInjectedInterface $second, string $name)
    {
    }

    public function doStuff(NotInjectedInterface $notInjected): void
    {
    }
}
