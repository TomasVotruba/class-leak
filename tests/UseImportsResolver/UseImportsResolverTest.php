<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Tests\UseImportsResolver;

use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use TomasVotruba\ClassLeak\Tests\AbstractTestCase;
use TomasVotruba\ClassLeak\Tests\UseImportsResolver\Fixture\SomeFactory;
use TomasVotruba\ClassLeak\Tests\UseImportsResolver\Source\FirstUsedClass;
use TomasVotruba\ClassLeak\Tests\UseImportsResolver\Source\FourthUsedClass;
use TomasVotruba\ClassLeak\Tests\UseImportsResolver\Source\SecondUsedClass;
use TomasVotruba\ClassLeak\UseImportsResolver;
use TomasVotruba\ClassLeak\ValueObject\FileWithClass;

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
    public function test(string $filePath, ?FileWithClass $fileWithClass, array $expectedClassUsages): void
    {
        $resolvedClassUsages = $this->useImportsResolver->resolve($filePath, $fileWithClass);
        $this->assertSame($expectedClassUsages, $resolvedClassUsages);
    }

    public static function provideData(): Iterator
    {
        yield [
            __DIR__ . '/Fixture/FileUsingOtherClasses.php',
            new FileWithClass(
                __DIR__ . '/Fixture/FileUsingOtherClasses.php',
                'FileUsingOtherClasses',
                false,
                false,
                [],
                [
                    'FirstUsedClass' => FirstUsedClass::class,
                    'SecondUsedClass' => SecondUsedClass::class,
                ],
            ),
            [FirstUsedClass::class, SecondUsedClass::class]
        ];
        yield [
            __DIR__ . '/Fixture/FileUsesStaticCall.php',
            new FileWithClass(
                __DIR__ . '/Fixture/FileUsesStaticCall.php',
                'FileUsesStaticCall',
                false,
                false,
                [],
                [
                    'FourthUsedClass' => FourthUsedClass::class,
                ],
            ),
            [SomeFactory::class, FourthUsedClass::class]
        ];
    }
}
