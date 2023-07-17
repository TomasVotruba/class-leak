<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Finder;

final class PhpFilesFinder
{
    /**
     * @return string[]
     */
    public function findPhpFiles(array $paths): array
    {
        // fallback to config paths
        if ($paths === []) {
            $paths = [getcwd()];
        }
        return $filePaths;
    }
}
