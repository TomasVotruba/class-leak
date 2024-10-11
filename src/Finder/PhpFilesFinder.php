<?php

declare (strict_types=1);
namespace TomasVotruba\ClassLeak\Finder;

use ClassLeak202410\Symfony\Component\Finder\Finder;
use ClassLeak202410\Symfony\Component\Finder\SplFileInfo;
use ClassLeak202410\Webmozart\Assert\Assert;
/**
 * @see \TomasVotruba\ClassLeak\Tests\Finder\PhpFilesFinderTest
 */
final class PhpFilesFinder
{
    /**
     * @param string[] $paths
     * @param string[] $fileExtensions
     * @return string[]
     */
    public function findPhpFiles(array $paths, array $fileExtensions) : array
    {
        Assert::allFileExists($paths);
        Assert::allString($fileExtensions);
        // fallback to config paths
        $filePaths = [];
        $currentFileFinder = Finder::create()->files()->in($paths)->sortByName();
        foreach ($fileExtensions as $fileExtension) {
            $currentFileFinder->name('*.' . $fileExtension);
        }
        foreach ($currentFileFinder as $fileInfo) {
            /** @var SplFileInfo $fileInfo */
            $filePaths[] = $fileInfo->getRealPath();
        }
        return $filePaths;
    }
}
