<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\ValueObject;

final readonly class ClassNames
{
    /**
     * @param string[] $attributes
     * @param array<string, string[]> $attributesByMethod
     */
    public function __construct(
        private string $className,
        private bool $hasParentClassOrInterface,
        private array $attributes,
        private array $attributesByMethod,
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
     * @return array<string, string[]>
     */
    public function getAttributesByMethod() : array
    {
        return $this->attributesByMethod;
    }
}
