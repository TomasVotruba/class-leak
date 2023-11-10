<?php

declare (strict_types=1);
namespace TomasVotruba\ClassLeak\Finder;

use ClassLeak202311\Webmozart\Assert\Assert;
/**
 * @see \TomasVotruba\ClassLeak\Tests\Finder\PhpFilesFinderTest
 */
final class PhpFilesFinder
{
    /**
     * @param string[] $paths
     * @return string[]
     */
    public function findPhpFiles(array $paths) : array
    {
        Assert::allFileExists($paths);
        // fallback to config paths
        $filePaths = [];
        foreach ($paths as $path) {
            if (\is_file($path)) {
                $filePaths[] = $path;
            } else {
                $currentFilePaths = $this->findFilesUsingGlob($path);
                $filePaths = \array_merge($filePaths, $currentFilePaths);
            }
        }
        \sort($filePaths);
        Assert::allString($filePaths);
        Assert::allFileExists($filePaths);
        return $filePaths;
    }
    /**
     * @return string[]
     */
    private function findFilesUsingGlob(string $directory) : array
    {
        // Search for php files in the current directory
        /** @var string[] $phpFiles */
        $phpFiles = \glob($directory . '/*.php');
        // recursively search in subdirectories
        /** @var string[] $subdirectories */
        $subdirectories = \glob($directory . '/*', \GLOB_ONLYDIR);
        foreach ($subdirectories as $subdirectory) {
            // Merge the results from subdirectories
            $phpFiles = \array_merge($phpFiles, $this->findFilesUsingGlob($subdirectory));
        }
        return $phpFiles;
    }
}
