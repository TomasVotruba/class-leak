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

    public function testSkipsClassWhoseInterfaceIsConstructorInjected(): void
    {
        $fileWithClass = $this->createSyncJudgeFileWithClass();

        $possiblyUnused = $this->possiblyUnusedClassesFilter->filter(
            [$fileWithClass],
            [],
            [],
            [],
            [],
            false,
            [SyncJudgeInterface::class],
        );

        $this->assertSame([], $possiblyUnused);
    }

    public function testKeepsClassWhoseInterfaceIsNotConstructorInjected(): void
    {
        $fileWithClass = $this->createSyncJudgeFileWithClass();

        $possiblyUnused = $this->possiblyUnusedClassesFilter->filter(
            [$fileWithClass],
            [],
            [],
            [],
            [],
            false,
            [],
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
