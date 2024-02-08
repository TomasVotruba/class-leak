<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\ValueObject;

final readonly class ClassNames
{
    public function __construct(
        private string $className,
        private bool $hasParentClassOrInterface
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
}
