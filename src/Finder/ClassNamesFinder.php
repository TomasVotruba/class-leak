<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Finder;

use TomasVotruba\ClassLeak\ClassNameResolver;
use TomasVotruba\ClassLeak\ValueObject\ClassNames;
use TomasVotruba\ClassLeak\ValueObject\FileWithClass;

final readonly class ClassNamesFinder
{
    public function __construct(
        private ClassNameResolver $classNameResolver,
    ) {
    }

    /**
     * @param string[] $filePaths
     * @return FileWithClass[]
     */
    public function resolveClassNamesToCheck(array $filePaths): array
    {
        $filesWithClasses = [];

        foreach ($filePaths as $filePath) {
            $classNames = $this->classNameResolver->resolveFromFromFilePath($filePath);
            if (! $classNames instanceof ClassNames) {
                continue;
            }

            $filesWithClasses[] = new FileWithClass(
                $filePath,
                $classNames->getClassName(),
                $classNames->hasParentClassOrInterface(),
                $classNames->hasApiTag(),
                $classNames->getUsedAttributes(),
            );
        }

        return $filesWithClasses;
    }
}
