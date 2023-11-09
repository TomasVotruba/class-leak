<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\ValueObject;

use TomasVotruba\ClassLeak\FileSystem\StaticRelativeFilePathHelper;

final class FileWithClass
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
}
