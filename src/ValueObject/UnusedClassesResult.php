<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\ValueObject;

final readonly class UnusedClassesResult
{
    /**
     * @param FileWithClass[] $withParentsFileWithClasses
     * @param FileWithClass[] $parentLessFileWithClasses
     * @param FileWithClass[] $traits
     */
    public function __construct(
        private array $parentLessFileWithClasses,
        private array $withParentsFileWithClasses,
        private array $traits,
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
        return count($this->parentLessFileWithClasses) + count($this->withParentsFileWithClasses) + count(
            $this->traits
        );
    }

    /**
     * @return FileWithClass[]
     */
    public function getTraits(): array
    {
        return $this->traits;
    }
}
