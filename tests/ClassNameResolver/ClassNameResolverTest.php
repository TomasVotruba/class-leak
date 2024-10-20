<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Tests\ClassNameResolver;

use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use TomasVotruba\ClassLeak\ClassNameResolver;
use TomasVotruba\ClassLeak\Tests\AbstractTestCase;
use TomasVotruba\ClassLeak\Tests\ClassNameResolver\Fixture\ClassWithAnyComment;
use TomasVotruba\ClassLeak\Tests\ClassNameResolver\Fixture\SomeAttribute;
use TomasVotruba\ClassLeak\Tests\ClassNameResolver\Fixture\SomeClass;
use TomasVotruba\ClassLeak\Tests\ClassNameResolver\Fixture\SomeMethodAttribute;
use TomasVotruba\ClassLeak\ValueObject\ClassNames;

final class ClassNameResolverTest extends AbstractTestCase
{
    private ClassNameResolver $classNameResolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->classNameResolver = $this->make(ClassNameResolver::class);
    }

    #[DataProvider('provideData')]
    public function test(string $filePath, ClassNames $expectedClassNames): void
    {
        $resolvedClassNames = $this->classNameResolver->resolveFromFilePath($filePath);

        $this->assertInstanceOf(ClassNames::class, $resolvedClassNames);
        $this->assertSame($expectedClassNames->getClassName(), $resolvedClassNames->getClassName());
        $this->assertSame(
            $expectedClassNames->hasParentClassOrInterface(),
            $resolvedClassNames->hasParentClassOrInterface()
        );
        $this->assertSame($expectedClassNames->getAttributes(), $resolvedClassNames->getAttributes());
    }

    public static function provideData(): Iterator
    {
        yield [
            __DIR__ . '/Fixture/SomeClass.php',
            new ClassNames(SomeClass::class, false, [SomeAttribute::class, SomeMethodAttribute::class]),
        ];

        yield [
            __DIR__ . '/Fixture/ClassWithAnyComment.php',
            new ClassNames(ClassWithAnyComment::class, false, []),
        ];
    }

    #[DataProvider('provideNoClassContainedData')]
    public function testNoClassContained(string $filePath): void
    {
        $resolvedClassNames = $this->classNameResolver->resolveFromFilePath($filePath);
        $this->assertNull($resolvedClassNames);
    }

    public static function provideNoClassContainedData(): Iterator
    {
        yield [__DIR__ . '/Fixture/ClassWithApiComment.php'];
    }
}
