<?php

declare (strict_types=1);
namespace TomasVotruba\ClassLeak\ValueObject;

use TomasVotruba\ClassLeak\FileSystem\StaticRelativeFilePathHelper;
final class FileWithClass
{
    /**
     * @readonly
     * @var string
     */
    private $filePath;
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
    public function __construct(string $filePath, string $className, bool $hasParentClassOrInterface)
    {
        $this->filePath = $filePath;
        $this->className = $className;
        $this->hasParentClassOrInterface = $hasParentClassOrInterface;
    }
    public function getClassName() : string
    {
        return $this->className;
    }
    public function getFilePath() : string
    {
        return StaticRelativeFilePathHelper::resolveFromCwd($this->filePath);
    }
    public function hasParentClassOrInterface() : bool
    {
        return $this->hasParentClassOrInterface;
    }
}
