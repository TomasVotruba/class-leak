<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Tests\Filtering;

use PHPUnit\Framework\TestCase;
use TomasVotruba\ClassLeak\Filtering\PossiblyUnusedClassesFilter;
use TomasVotruba\ClassLeak\Tests\Filtering\Fixture\SyncJudge;
use TomasVotruba\ClassLeak\Tests\Filtering\Fixture\SyncJudgeInterface;
use TomasVotruba\ClassLeak\ValueObject\FileWithClass;

final class PossiblyUnusedClassesFilterTest extends TestCase
{
    private PossiblyUnusedClassesFilter $possiblyUnusedClassesFilter;

    protected function setUp(): void
    {
        $this->possiblyUnusedClassesFilter = new PossiblyUnusedClassesFilter();
    }

    public function testSkipsClassWhoseInterfaceIsUsedElsewhere(): void
    {
        $fileWithClass = $this->createSyncJudgeFileWithClass();

        // interface referenced in 2 files: the implementing class and one consumer
        $usedNameCounts = [
            SyncJudgeInterface::class => 2,
        ];

        $possiblyUnused = $this->possiblyUnusedClassesFilter->filter(
            [$fileWithClass],
            [],
            [],
            [],
            [],
            false,
            $usedNameCounts,
        );

        $this->assertSame([], $possiblyUnused);
    }

    public function testKeepsClassWhoseInterfaceIsUsedOnlyByItself(): void
    {
        $fileWithClass = $this->createSyncJudgeFileWithClass();

        // interface referenced only in the implementing class file
        $usedNameCounts = [
            SyncJudgeInterface::class => 1,
        ];

        $possiblyUnused = $this->possiblyUnusedClassesFilter->filter(
            [$fileWithClass],
            [],
            [],
            [],
            [],
            false,
            $usedNameCounts,
        );

        $this->assertSame([$fileWithClass], $possiblyUnused);
    }

    private function createSyncJudgeFileWithClass(): FileWithClass
    {
        return new FileWithClass(
            __DIR__ . '/Fixture/SyncJudge.php',
            SyncJudge::class,
            true,
            [],
            [SyncJudgeInterface::class],
        );
    }
}
