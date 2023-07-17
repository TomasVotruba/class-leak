<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Tests\ActiveClass\ClassNameResolver;

use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use TomasVotruba\ClassLeak\ClassNameResolver;
use TomasVotruba\ClassLeak\Tests\AbstractTestCase;
use TomasVotruba\ClassLeak\Tests\ActiveClass\ClassNameResolver\Fixture\SomeClass;

final class ClassNameResolverTest extends AbstractTestCase
{
    private ClassNameResolver $classNameResolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->classNameResolver = $this->make(ClassNameResolver::class);
    }

    /**
     * @param class-string $expectedClassName
     */
    #[DataProvider('provideData')]
    public function test(string $filePath, string $expectedClassName): void
    {
        $resolvedClassName = $this->classNameResolver->resolveFromFromFilePath($filePath);
        $this->assertSame($expectedClassName, $resolvedClassName);
    }

    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SomeClass.php', SomeClass::class];
    }
}
