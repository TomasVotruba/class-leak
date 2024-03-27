<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\ValueObject;

final readonly class ClassNames
{
    /**
     * @param string[] $usedAttributes
     */
    public function __construct(
        private string $className,
        private bool $hasParentClassOrInterface,
        private bool $hasApiTag,
        private array $usedAttributes,
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

    public function hasApiTag() : bool
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
}
