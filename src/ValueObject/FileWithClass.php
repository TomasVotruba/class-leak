<?php

declare (strict_types=1);
namespace TomasVotruba\ClassLeak\ValueObject;

use JsonSerializable;
use ClassLeak202412\Nette\Utils\FileSystem;
use TomasVotruba\ClassLeak\FileSystem\StaticRelativeFilePathHelper;
final class FileWithClass implements JsonSerializable
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
    /**
     * @var string[]
     * @readonly
     */
    private $attributes;
    /**
     * @param string[] $attributes
     */
    public function __construct(string $filePath, string $className, bool $hasParentClassOrInterface, array $attributes)
    {
        $this->filePath = $filePath;
        $this->className = $className;
        $this->hasParentClassOrInterface = $hasParentClassOrInterface;
        $this->attributes = $attributes;
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
    /**
     * @return string[]
     */
    public function getAttributes() : array
    {
        return $this->attributes;
    }
    /**
     * @return array{file_path: string, class: string, attributes: string[]}
     */
    public function jsonSerialize() : array
    {
        return ['file_path' => $this->filePath, 'class' => $this->className, 'attributes' => $this->attributes];
    }
    /**
     * Is serialized, could be hidden inside json output magic
     */
    public function isSerialized() : bool
    {
        $fileContents = FileSystem::read($this->filePath);
        return \strpos($fileContents, '@Serializer') !== \false;
    }
    /**
     * Dummy check for Doctrine ORM/ODM entity
     */
    public function isEntity() : bool
    {
        $fileContents = FileSystem::read($this->filePath);
        if (\strpos($fileContents, 'ClassLeak202412\\Doctrine\\ODM\\MongoDB\\Mapping\\Annotations') !== \false) {
            return \true;
        }
        if (\strpos($fileContents, 'ClassLeak202412\\Doctrine\\ORM\\Annotations') !== \false) {
            return \true;
        }
        if (\strpos($fileContents, '@ORM\\Entity') !== \false) {
            return \true;
        }
        if (\strpos($fileContents, '@Entity') !== \false) {
            return \true;
        }
        if (\strpos($fileContents, '@ODM\\Document') !== \false) {
            return \true;
        }
        return \strpos($fileContents, '@Document') !== \false;
    }
    public function isTrait() : bool
    {
        return \trait_exists($this->className);
    }
}
