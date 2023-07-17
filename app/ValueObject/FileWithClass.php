<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\ValueObject;

use TomasVotruba\ClassLeak\FileSystem\StaticRelativeFilePathHelper;
use Symplify\SmartFileSystem\SmartFileInfo;

final class FileWithClass
{
    public function __construct(
        private readonly string $filePath,
        private readonly string $className
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
}
