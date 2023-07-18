<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Finder;

use TomasVotruba\ClassLeak\ClassNameResolver;
use TomasVotruba\ClassLeak\ValueObject\FileWithClass;

final class ClassNamesFinder
{
    /**
     * @readonly
     * @var \TomasVotruba\ClassLeak\ClassNameResolver
     */
    private $classNameResolver;
    public function __construct(ClassNameResolver $classNameResolver)
    {
        $this->classNameResolver = $classNameResolver;
    }
    /**
     * @param string[] $filePaths
     * @return FileWithClass[]
     */
    public function resolveClassNamesToCheck(array $filePaths): array
    {
        $filesWithClasses = [];

        foreach ($filePaths as $filePath) {
            $className = $this->classNameResolver->resolveFromFromFilePath($filePath);
            if ($className === null) {
                continue;
            }

            $filesWithClasses[] = new FileWithClass($filePath, $className);
        }

        return $filesWithClasses;
    }
}
