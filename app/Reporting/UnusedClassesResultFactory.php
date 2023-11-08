<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Reporting;

use TomasVotruba\ClassLeak\ValueObject\FileWithClass;
use TomasVotruba\ClassLeak\ValueObject\UnusedClassesResult;

final class UnusedClassesResultFactory
{
    /**
     * @param FileWithClass[] $unusedFilesWithClasses
     */
    public function create(array $unusedFilesWithClasses): UnusedClassesResult
    {
        $parentLessFileWithClasses = [];
        $withParentsFileWithClasses = [];

        foreach ($unusedFilesWithClasses as $unusedFileWithClass) {
            if ($unusedFileWithClass->hasParentClassOrInterface()) {
                $withParentsFileWithClasses[] = $unusedFileWithClass;
            } else {
                $parentLessFileWithClasses[] = $unusedFileWithClass;
            }
        }

        return new UnusedClassesResult($parentLessFileWithClasses, $withParentsFileWithClasses);
    }
}
