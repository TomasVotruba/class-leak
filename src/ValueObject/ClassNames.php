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
     * @var string[]
     */
    private array $parentTypeNames;

    /**
     * @param string[] $attributes
     * @param string[] $interfaceNames
     * @param string[] $parentTypeNames
     */
    public function __construct(
        string $className,
        bool $hasParentClassOrInterface,
        array $attributes,
        array $interfaceNames = [],
        array $parentTypeNames = []
    ) {
        $this->className = $className;
        $this->hasParentClassOrInterface = $hasParentClassOrInterface;
        $this->attributes = $attributes;
        $this->interfaceNames = $interfaceNames;
        $this->parentTypeNames = $parentTypeNames;
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

    /**
     * @return string[]
     */
    public function getParentTypeNames(): array
    {
        return $this->parentTypeNames;
    }
}
