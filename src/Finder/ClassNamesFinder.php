<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Finder;

use Closure;
use TomasVotruba\ClassLeak\ClassNameResolver;
use TomasVotruba\ClassLeak\ValueObject\ClassNames;
use TomasVotruba\ClassLeak\ValueObject\FileWithClass;

final class ClassNamesFinder
{
    private ClassNameResolver $classNameResolver;

    public function __construct(ClassNameResolver $classNameResolver)
    {
        $this->classNameResolver = $classNameResolver;
    }

    /**
     * @param string[] $filePaths
     * @return FileWithClass[]
     */
    public function resolveClassNamesToCheck(array $filePaths, ?Closure $progressCallback): array
    {
        $filesWithClasses = [];
        foreach ($filePaths as $filePath) {
            if ($progressCallback instanceof Closure) {
                $progressCallback->__invoke();
            }

            $classNames = $this->classNameResolver->resolveFromFilePath($filePath);
            if (! $classNames instanceof ClassNames) {
                continue;
            }

            $filesWithClasses[] = new FileWithClass(
                $filePath,
                $classNames->getClassName(),
                $classNames->hasParentClassOrInterface(),
                $classNames->getAttributes(),
                $classNames->getInterfaceNames(),
            );
        }

        return $filesWithClasses;
    }
}
