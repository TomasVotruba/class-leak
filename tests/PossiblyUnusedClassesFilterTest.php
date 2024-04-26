<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use TomasVotruba\ClassLeak\Filtering\PossiblyUnusedClassesFilter;
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
    )
    {
        $filter = new PossiblyUnusedClassesFilter();
        $this->assertSame($expectedResult, $filter->filter(
            $filesWithClasses,
            $usedClassNames,
            $typesToSkip,
            $suffixesToSkip,
            $attributesToSkip
        ));
    }

    public static function provideData(): \Iterator
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
