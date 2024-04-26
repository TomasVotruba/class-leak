<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Tests\Filtering;

use Iterator;
use TomasVotruba\ClassLeak\Filtering\PossiblyUnusedClassesFilter;
use TomasVotruba\ClassLeak\Tests\AbstractTestCase;
use TomasVotruba\ClassLeak\ValueObject\FileWithClass;

final class PossiblyUnusedClassesFilterTest extends AbstractTestCase
{
    /**
     * @param FileWithClass[] $filesWithClasses
     * @param string[] $usedClassNames
     * @param string[] $typesToSkip
     * @param string[] $suffixesToSkip
     * @param string[] $attributesToSkip
     *
     * @dataProvider provideData
     */
    public function testFilter(
        array $expectedResult,
        array $filesWithClasses,
        array $usedClassNames,
        array $typesToSkip,
        array $suffixesToSkip,
        array $attributesToSkip
    ): void
    {
        $possiblyUnusedClassesFilter = new PossiblyUnusedClassesFilter();
        $this->assertSame($expectedResult, $possiblyUnusedClassesFilter->filter(
            $filesWithClasses,
            $usedClassNames,
            $typesToSkip,
            $suffixesToSkip,
            $attributesToSkip
        ));
    }

    public static function provideData(): Iterator
    {
        yield [ // test case-insensitive class name
            [],
            [new FileWithClass('foo.php', 'Bar', false, [])],
            ['BAR'],
            [],
            [],
            []
        ];

        yield [ // test case-insensitive skip suffixes
            [],
            [new FileWithClass('foo.php', 'Bar', false, [])],
            [],
            [],
            ['AR'],
            []
        ];

    }
}
