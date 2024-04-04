<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Tests\Functional\Fixture\Fixture1;

final readonly class MyValue
{
    public function __construct(
        public string $value,
    ) {}
}
