<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Finder;

use Symfony\Component\Console\Helper\ProgressBar;
use TomasVotruba\ClassLeak\ClassNameResolver;
use TomasVotruba\ClassLeak\ValueObject\ClassNames;
use TomasVotruba\ClassLeak\ValueObject\FileWithClass;

final readonly class ClassNamesFinder
{
    public function __construct(
        private ClassNameResolver $classNameResolver
    ) {
    }

    /**
     * @param string[] $filePaths
     * @return FileWithClass[]
     */
    public function resolveClassNamesToCheck(array $filePaths, ?ProgressBar $progressBar): array
    {
        $filesWithClasses = [];
        foreach ($filePaths as $filePath) {
            $progressBar?->advance();

            $classNames = $this->classNameResolver->resolveFromFilePath($filePath);
            if (! $classNames instanceof ClassNames) {
                continue;
            }

            $filesWithClasses[] = new FileWithClass(
                $filePath,
                $classNames->getClassName(),
                $classNames->hasParentClassOrInterface(),
                $classNames->getAttributes(),
            );
        }

        return $filesWithClasses;
    }
}
