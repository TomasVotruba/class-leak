<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Finder;

use Entropy\FileSystem\FileFinder;
use Entropy\FileSystem\FileInfo;
use InvalidArgumentException;

/**
 * @see \TomasVotruba\ClassLeak\Tests\Finder\PhpFilesFinderTest
 */
final class PhpFilesFinder
{
    /**
     * @param string[] $paths
     * @param string[] $fileExtensions
     * @param string[] $pathsToSkip
     *
     * @return string[]
     */
    public function findPhpFiles(array $paths, array $fileExtensions, array $pathsToSkip): array
    {
        foreach ($paths as $path) {
            if (! file_exists($path)) {
                throw new InvalidArgumentException(sprintf('Path "%s" was not found', $path));
            }
        }

        // skip-path option supports both directory names (e.g. "vendor") and
        // real/relative paths (e.g. "lib/vendor"). Split them: match simple
        // directory names anywhere in the tree, and the rest by realpath.
        $excludedDirectoryNames = [];
        $excludedRealPaths = [];
        foreach ($pathsToSkip as $pathToSkip) {
            if (! str_contains($pathToSkip, '/') && ! str_contains($pathToSkip, '\\')) {
                $excludedDirectoryNames[] = $pathToSkip;
                continue;
            }

            $realPath = realpath($pathToSkip);
            if ($realPath !== false) {
                $excludedRealPaths[] = $realPath;
            }
        }

        $fileInfos = FileFinder::find($paths, function (FileInfo $fileInfo) use (
            $fileExtensions,
            $excludedDirectoryNames,
            $excludedRealPaths
        ): bool {
            if (! in_array($fileInfo->getExtension(), $fileExtensions, true)) {
                return false;
            }

            $realPath = (string) $fileInfo->getRealPath();

            if ($this->isWithinExcludedDirectoryName($realPath, $excludedDirectoryNames)) {
                return false;
            }

            return ! $this->isWithinExcludedPath($realPath, $excludedRealPaths);
        });

        $filePaths = [];
        foreach ($fileInfos as $fileInfo) {
            $filePaths[] = (string) $fileInfo->getRealPath();
        }

        return $filePaths;
    }

    /**
     * @param string[] $excludedDirectoryNames
     */
    private function isWithinExcludedDirectoryName(string $realPath, array $excludedDirectoryNames): bool
    {
        if ($excludedDirectoryNames === []) {
            return false;
        }

        $pathParts = explode(DIRECTORY_SEPARATOR, $realPath);

        return array_intersect($excludedDirectoryNames, $pathParts) !== [];
    }

    /**
     * @param string[] $excludedRealPaths
     */
    private function isWithinExcludedPath(string $realPath, array $excludedRealPaths): bool
    {
        foreach ($excludedRealPaths as $excludedRealPath) {
            if ($realPath === $excludedRealPath) {
                return true;
            }

            if (str_starts_with($realPath, $excludedRealPath . DIRECTORY_SEPARATOR)) {
                return true;
            }
        }

        return false;
    }
}
