<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Tests\UseImportsResolver;

use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use TomasVotruba\ClassLeak\Tests\AbstractTestCase;
use TomasVotruba\ClassLeak\Tests\UseImportsResolver\Source\FirstUsedClass;
use TomasVotruba\ClassLeak\Tests\UseImportsResolver\Source\SecondUsedClass;
use TomasVotruba\ClassLeak\UseImportsResolver;

final class UseImportsResolverTest extends AbstractTestCase
{
    private UseImportsResolver $useImportsResolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->useImportsResolver = $this->make(UseImportsResolver::class);
    }

    /**
     * @param string[] $expectedClassUsages
     */
    #[DataProvider('provideData')]
    public function test(string $filePath, array $expectedClassUsages): void
    {
        $resolvedClassUsages = $this->useImportsResolver->resolve($filePath);
        $this->assertSame($expectedClassUsages, $resolvedClassUsages);
    }

    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/FileUsingOtherClasses.php', [FirstUsedClass::class, SecondUsedClass::class]];
        yield [__DIR__ . '/Fixture/PHPDocTyped.php', [FirstUsedClass::class, SecondUsedClass::class]];
    }
}
