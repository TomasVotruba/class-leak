<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Tests\Functional\Fixture\Fixture1;

final readonly class MyResponse
{
    /**
     * @param list<MyValue> $values
     */
    public function __construct(
        public array $values,
    ) {}
}
