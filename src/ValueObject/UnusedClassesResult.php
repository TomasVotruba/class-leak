<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\ValueObject;

final readonly class UnusedClassesResult
{
    /**
     * @param FileWithClass[] $withParentsFileWithClasses
     * @param FileWithClass[] $parentLessFileWithClasses
     */
    public function __construct(
        private array $parentLessFileWithClasses,
        private array $withParentsFileWithClasses,
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
