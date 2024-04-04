<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Tests\ClassNameResolver\Fixture;

use DateTime;
use TomasVotruba\ClassLeak\Tests\ClassNameResolver\Fixture\Attributes\SomeAttribute;

/**
 * @api
 */
#[SomeAttribute]
final class SomeClass
{
    #[SomeMethodAttribute]
    public function myMethod(): DateTime
    {
    }
}
