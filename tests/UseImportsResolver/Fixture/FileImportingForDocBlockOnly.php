<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Tests\UseImportsResolver\Fixture;

use TomasVotruba\ClassLeak\Tests\UseImportsResolver\Source\ThirdUsedClass;

final class FileImportingForDocBlockOnly
{
    /**
     * @param ThirdUsedClass[] $items
     */
    public function run(array $items): void
    {
    }
}
