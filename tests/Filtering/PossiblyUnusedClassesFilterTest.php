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

    public function testSkipsInterfaceImplementedAtLeastOnce(): void
    {
        $interfaceFileWithClass = new FileWithClass(
            __DIR__ . '/Fixture/SyncJudgeInterface.php',
            SyncJudgeInterface::class,
            false,
            [],
            [],
        );

        $syncJudgeFileWithClass = $this->createSyncJudgeFileWithClass();

        $possiblyUnused = $this->possiblyUnusedClassesFilter->filter(
            [$interfaceFileWithClass, $syncJudgeFileWithClass],
            [],
            [],
            [],
            [],
            false,
            [],
        );

        $this->assertSame([$syncJudgeFileWithClass], $possiblyUnused);
    }

    public function testSkipsDeclaredSubtypeOfSkippedTypeWithoutAutoloading(): void
    {
        // neither class nor parent is autoloadable, as in a project scanned without its vendor
        $fileWithClass = new FileWithClass(
            __DIR__ . '/Fixture/SyncJudge.php',
            'App\NotAutoloaded\SomeTest',
            true,
            [],
            [],
            ['Vendor\NotAutoloaded\TestCase'],
        );

        $possiblyUnused = $this->possiblyUnusedClassesFilter->filter(
            [$fileWithClass],
            [],
            ['Vendor\NotAutoloaded\TestCase'],
            [],
            [],
            false,
        );

        $this->assertSame([], $possiblyUnused);
    }

    public function testSkipsDeclaredSubtypeThroughScannedParent(): void
    {
        $abstractFileWithClass = new FileWithClass(
            __DIR__ . '/Fixture/SyncJudge.php',
            'App\NotAutoloaded\AbstractTest',
            true,
            [],
            [],
            ['Vendor\NotAutoloaded\TestCase'],
        );

        $childFileWithClass = new FileWithClass(
            __DIR__ . '/Fixture/SyncJudge.php',
            'App\NotAutoloaded\ChildTest',
            true,
            [],
            [],
            ['App\NotAutoloaded\AbstractTest'],
        );

        $unrelatedFileWithClass = new FileWithClass(
            __DIR__ . '/Fixture/SyncJudge.php',
            'App\NotAutoloaded\Unrelated',
            true,
            [],
            [],
            ['Vendor\NotAutoloaded\Other'],
        );

        $possiblyUnused = $this->possiblyUnusedClassesFilter->filter(
            [$abstractFileWithClass, $childFileWithClass, $unrelatedFileWithClass],
            ['App\NotAutoloaded\AbstractTest'],
            ['Vendor\NotAutoloaded\TestCase'],
            [],
            [],
            false,
        );

        $this->assertSame([$unrelatedFileWithClass], $possiblyUnused);
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
