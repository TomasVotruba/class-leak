<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\ValueObject;

final readonly class ClassNames
{
    /**
     * @param string[] $usedAttributes
     * @param array<string, string> $imports
     */
    public function __construct(
        private string $className,
        private bool $hasParentClassOrInterface,
        private bool $hasApiTag,
        private array $usedAttributes,
        private array $imports,
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

    public function hasApiTag(): bool
    {
        return $this->hasApiTag;
    }

    /**
     * @return string[]
     */
    public function getUsedAttributes(): array
    {
        return $this->usedAttributes;
    }

    /**
     * @return array<string, string>
     */
    public function getImports(): array
    {
        return $this->imports;
    }
}
