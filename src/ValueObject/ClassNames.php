<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\ValueObject;

final readonly class ClassNames
{
    /**
     * @param string[] $attributes
     * @param string[] $interfaceNames
     */
    public function __construct(
        private string $className,
        private bool $hasParentClassOrInterface,
        private array $attributes,
        private array $interfaceNames = [],
    ) {
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function hasParentClassOrInterface(): bool
    {
        return $this->hasParentClassOrInterface;
    }

    /**
     * @return string[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return string[]
     */
    public function getInterfaceNames(): array
    {
        return $this->interfaceNames;
    }
}
