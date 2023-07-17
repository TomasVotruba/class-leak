<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Tests\ActiveClass\UseImportsResolver;

use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use TomasVotruba\ClassLeak\Tests\AbstractTestCase;
use TomasVotruba\ClassLeak\Tests\ActiveClass\UseImportsResolver\Source\FirstUsedClass;
use TomasVotruba\ClassLeak\Tests\ActiveClass\UseImportsResolver\Source\SecondUsedClass;
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
     * @param string[] $filePaths
     * @param string[] $expectedClassUsages
     */
    #[DataProvider('provideData')]
    public function test(array $filePaths, array $expectedClassUsages): void
    {
        $resolvedClassUsages = $this->useImportsResolver->resolveFromFilePaths($filePaths);
        $this->assertSame($expectedClassUsages, $resolvedClassUsages);
    }

    public static function provideData(): Iterator
    {
        yield [[__DIR__ . '/Fixture/FileUsingOtherClasses.php'], [FirstUsedClass::class, SecondUsedClass::class]];
    }
}
