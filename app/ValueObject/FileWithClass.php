<?php

declare(strict_types=1);

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
    public function __construct(string $filePath, string $className)
    {
        $this->filePath = $filePath;
        $this->className = $className;
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
