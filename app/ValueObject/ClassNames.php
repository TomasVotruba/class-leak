<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\ValueObject;

final class ClassNames
{
    public function __construct(
        private readonly string $className,
        private readonly bool $hasParentClassOrInterface
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
