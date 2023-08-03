<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Tests\UseImportsResolver\Fixture;

final class PHPDocTyped
{
    /**
     * @param \TomasVotruba\ClassLeak\Tests\UseImportsResolver\Source\FirstUsedClass $firstUsed
     *
     * @return \TomasVotruba\ClassLeak\Tests\UseImportsResolver\Source\ThirdUsedClass
     */
    public function run($firstUsed)
    {
        /** @var \TomasVotruba\ClassLeak\Tests\UseImportsResolver\Source\SecondUsedClass $x */
        $x = someFunction();
        return $x;
    }
}
