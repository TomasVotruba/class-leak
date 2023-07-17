<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Finder;

use Symfony\Component\Finder\Finder;
use Webmozart\Assert\Assert;

/**
 * @see \TomasVotruba\ClassLeak\Tests\Finder\PhpFilesFinderTest
 */
final class PhpFilesFinder
{
    /**
     * @param string[] $paths
     * @return string[]
     */
    public function findPhpFiles(array $paths): array
    {
        Assert::allFileExists($paths);

        // fallback to config paths
        $filePaths = [];

        foreach ($paths as $path) {
            if (is_file($path)) {
                $filePaths[] = $path;
            } else {
                $phpFilesFinder = Finder::create()
                    ->files()
                    ->in($path)
                    ->name('*.php');

                foreach ($phpFilesFinder->getIterator() as $fileInfo) {
                    $filePaths[] = $fileInfo->getPathname();
                }
            }
        }

        sort($filePaths);

        Assert::allString($filePaths);
        Assert::allFileExists($filePaths);

        return $filePaths;
    }
}
