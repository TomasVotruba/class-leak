<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Finder;

use TomasVotruba\ClassLeak\Exception\ShouldNotHappenException;
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
        // fallback to config paths
        $filePaths = [];

        foreach ($paths as $path) {
            if (is_file($path)) {
                $filePaths[] = $path;
            } else {
                // @see https://stackoverflow.com/a/36034646/1348344
                $directoryFilePaths = glob($path . '/{**/*,*}.php', GLOB_BRACE);
                if ($directoryFilePaths === false) {
                    continue;
                }

                $filePaths = array_merge($filePaths, $directoryFilePaths);
            }
        }

        Assert::allString($filePaths);
        Assert::allFileExists($filePaths);

        return $filePaths;
    }
}
