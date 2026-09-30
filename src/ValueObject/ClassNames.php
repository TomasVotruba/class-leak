<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\ValueObject;

final class ClassNames
{
    private string $className;

    private bool $hasParentClassOrInterface;

    /**
     * @var string[]
     */
    private array $attributes;

    /**
     * @var string[]
     */
    private array $interfaceNames;

    /**
     * @param string[] $attributes
     * @param string[] $interfaceNames
     */
    public function __construct(
        string $className,
        bool $hasParentClassOrInterface,
        array $attributes,
        array $interfaceNames = []
    ) {
        $this->className = $className;
        $this->hasParentClassOrInterface = $hasParentClassOrInterface;
        $this->attributes = $attributes;
        $this->interfaceNames = $interfaceNames;
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
