<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\ValueObject;

use JsonSerializable;
use TomasVotruba\ClassLeak\FileSystem\StaticRelativeFilePathHelper;

final class FileWithClass implements JsonSerializable
{
    public function __construct(
        private readonly string $filePath,
        private readonly string $className,
        private readonly bool $hasParentClassOrInterface
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

    /**
     * @return array{file_path: string, class: string}
     */
    public function jsonSerialize(): array
    {
        return [
            'file_path' => $this->filePath,
            'class' => $this->className,
        ];
    }
}
