<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Tests\Filtering;

use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TomasVotruba\ClassLeak\Filtering\PossiblyUnusedClassesFilter;
use TomasVotruba\ClassLeak\ValueObject\FileWithClass;

final class PossiblyUnusedClassesFilterTest extends TestCase
{
    private PossiblyUnusedClassesFilter $filter;

    protected function setUp() : void
    {
        parent::setUp();

        $this->filter = new PossiblyUnusedClassesFilter();
    }

    /**
     * @param FileWithClass[] $filesWithClasses
     * @param string[] $usedClassNames
     * @param string[] $typesToSkip
     * @param string[] $suffixesToSkip
     * @param string[] $attributesToSkip
     */
    #[DataProvider('provideData')]
    public function test(
        array $filesWithClasses,
        array $usedClassNames,
        array $typesToSkip,
        array $suffixesToSkip,
        array $attributesToSkip,
        array $expectedFilteredFilesWithClasses,
    ) : void {
        self::assertEquals(
            $expectedFilteredFilesWithClasses,
            $this->filter->filter(
                $filesWithClasses,
                $usedClassNames,
                $typesToSkip,
                $suffixesToSkip,
                $attributesToSkip,
            ),
        );
    }

    public static function provideData() : iterable
    {
        yield 'it should not filter' => [
            [
                new FileWithClass(
                    'some-file.php',
                    'SomeClass',
                    false,
                    false,
                    [],
                ),
            ],
            [],
            [],
            [],
            [],
            [
                new FileWithClass(
                    'some-file.php',
                    'SomeClass',
                    false,
                    false,
                    [],
                ),
            ],
        ];

        yield 'it should filter class with api tag' => [
            [
                new FileWithClass(
                    'some-file.php',
                    'SomeClass',
                    false,
                    true,
                    [],
                ),
            ],
            [],
            [],
            [],
            [],
            [],
        ];
    }
}
