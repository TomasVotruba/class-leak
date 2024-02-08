<?php

declare (strict_types=1);
namespace TomasVotruba\ClassLeak\ValueObject;

final class ClassNames
{
    /**
     * @readonly
     * @var string
     */
    private $className;
    /**
     * @readonly
     * @var bool
     */
    private $hasParentClassOrInterface;
    public function __construct(string $className, bool $hasParentClassOrInterface)
    {
        $this->className = $className;
        $this->hasParentClassOrInterface = $hasParentClassOrInterface;
    }
    public function getClassName() : string
    {
        return $this->className;
    }
    public function hasParentClassOrInterface() : bool
    {
        return $this->hasParentClassOrInterface;
    }
}
