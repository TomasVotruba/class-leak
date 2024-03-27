<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\ValueObject;

use JsonSerializable;
use TomasVotruba\ClassLeak\FileSystem\StaticRelativeFilePathHelper;

final readonly class FileWithClass implements JsonSerializable
{
    /**
     * @param string[] $usedAttributes
     */
    public function __construct(
        private string $filePath,
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

    public function getFilePath(): string
    {
        return StaticRelativeFilePathHelper::resolveFromCwd($this->filePath);
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
     * @return array{
     *     file_path: string,
     *     class: string,
     *     has_parent_class_or_interface: bool,
     *     has_api_tag: bool,
     *     used_attributes: string[]
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'file_path' => $this->filePath,
            'class' => $this->className,
            'has_parent_class_or_interface' => $this->hasParentClassOrInterface,
            'has_api_tag' => $this->hasApiTag,
            'used_attributes' => $this->usedAttributes,
        ];
    }
}
