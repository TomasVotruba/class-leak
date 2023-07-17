<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Tests\Finder;

use TomasVotruba\ClassLeak\Finder\PhpFilesFinder;
use TomasVotruba\ClassLeak\Tests\AbstractTestCase;

final class PhpFilesFinderTest extends AbstractTestCase
{
    private PhpFilesFinder $phpFilesFinder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->phpFilesFinder = $this->make(PhpFilesFinder::class);
    }

    public function test(): void
    {
        $phpFiles = $this->phpFilesFinder->findPhpFiles([__DIR__ . '/Fixture']);
        $this->assertCount(3, $phpFiles);
    }
}
