<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\ValueObject;

final class UnusedClassesResult
{
    /**
     * @param FileWithClass[] $withParentsFileWithClasses
     * @param FileWithClass[] $parentLessFileWithClasses
     */
    public function __construct(
        private readonly array $parentLessFileWithClasses,
        private readonly array $withParentsFileWithClasses,
    ) {
    }

    /**
     * @return FileWithClass[]
     */
    public function getParentLessFileWithClasses(): array
    {
        return $this->parentLessFileWithClasses;
    }

    /**
     * @return FileWithClass[]
     */
    public function getWithParentsFileWithClasses(): array
    {
        return $this->withParentsFileWithClasses;
    }

    public function getCount(): int
    {
        return count($this->parentLessFileWithClasses) + count($this->withParentsFileWithClasses);
    }
}
