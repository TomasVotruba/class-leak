<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Tests\ClassNameResolver\Fixture;

/**
 * @api
 */
#[SomeAttribute]
final class SomeClass
{
    #[SomeMethodAttribute]
    public function myMethod(): void
    {
    }
}
