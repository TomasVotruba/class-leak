<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Tests\ConstructorParamTypeResolver\Fixture;

use TomasVotruba\ClassLeak\Tests\ConstructorParamTypeResolver\Source\FirstInjectedInterface;
use TomasVotruba\ClassLeak\Tests\ConstructorParamTypeResolver\Source\SecondInjectedInterface;
use TomasVotruba\ClassLeak\Tests\ConstructorParamTypeResolver\Source\NotInjectedInterface;

final class WithConstructorInjection
{
    public function __construct(
        private readonly FirstInjectedInterface $first,
        private readonly ?SecondInjectedInterface $second,
        private readonly string $name,
    ) {
    }

    public function doStuff(NotInjectedInterface $notInjected): void
    {
    }
}
