<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Tests\Filtering;

use InvalidArgumentException;
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
            false,
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
            false,
            [],
            [],
            ['Vendor\NotAutoloaded\TestCase'],
        );

        $childFileWithClass = new FileWithClass(
            __DIR__ . '/Fixture/SyncJudge.php',
            'App\NotAutoloaded\ChildTest',
            true,
            false,
            [],
            [],
            ['App\NotAutoloaded\AbstractTest'],
        );

        $unrelatedFileWithClass = new FileWithClass(
            __DIR__ . '/Fixture/SyncJudge.php',
            'App\NotAutoloaded\Unrelated',
            true,
            false,
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

    public function testSkipsControllerAndTestByDefaultSuffix(): void
    {
        $testFileWithClass = new FileWithClass(
            __DIR__ . '/Fixture/SyncJudge.php',
            'App\NotAutoloaded\SomeTest',
            true,
            false,
            [],
            [],
            ['App\NotAutoloaded\AbstractMysqlTestCase'],
        );

        $abstractTestCaseFileWithClass = new FileWithClass(
            __DIR__ . '/Fixture/SyncJudge.php',
            'App\NotAutoloaded\AbstractMysqlTestCase',
            true,
            false,
            [],
            [],
            ['Vendor\NotAutoloaded\WebTestCase'],
        );

        $controllerFileWithClass = new FileWithClass(
            __DIR__ . '/Fixture/SyncJudge.php',
            'App\NotAutoloaded\KeepAliveController',
            false,
            false,
            [],
            [],
        );

        $serviceFileWithClass = new FileWithClass(
            __DIR__ . '/Fixture/SyncJudge.php',
            'App\NotAutoloaded\ControllerResolver',
            false,
            false,
            [],
            [],
        );

        $possiblyUnused = $this->possiblyUnusedClassesFilter->filter(
            [$testFileWithClass, $abstractTestCaseFileWithClass, $controllerFileWithClass, $serviceFileWithClass],
            [],
            [],
            [],
            [],
            false,
        );

        $this->assertSame([$serviceFileWithClass], $possiblyUnused);
    }

    public function testThrowsOnShortAttributeToSkip(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->possiblyUnusedClassesFilter->filter([], [], [], [], ['AsEventListener'], false);
    }

    public function testAllowsNativeShortAttributeToSkip(): void
    {
        $possiblyUnused = $this->possiblyUnusedClassesFilter->filter([], [], [], [], ['Deprecated'], false);

        $this->assertSame([], $possiblyUnused);
    }

    private function createSyncJudgeFileWithClass(): FileWithClass
    {
        return new FileWithClass(
            __DIR__ . '/Fixture/SyncJudge.php',
            SyncJudge::class,
            true,
            false,
            [],
            [SyncJudgeInterface::class],
        );
    }
}
