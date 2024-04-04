<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\ValueObject;

use JsonSerializable;
use TomasVotruba\ClassLeak\FileSystem\StaticRelativeFilePathHelper;

final readonly class FileWithClass implements JsonSerializable
{
    private ?string $namespace;

    /**
     * @param string[] $usedAttributes
     * @param array<string, string> $imports
     */
    public function __construct(
        private string $filePath,
        private string $className,
        private bool $hasParentClassOrInterface,
        private bool $hasApiTag,
        private array $usedAttributes,
        private array $imports,
    ) {
        $pos = strrpos($this->className, '\\');
        $this->namespace = $pos !== false ? substr($this->className, 0, $pos) : null;
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

    public function resolveName(string $name): string
    {
        if (str_starts_with($name, '\\')) {
            return ltrim($name, '\\');
        }

        $nameParts = explode('\\', $name);
        $firstNamePart = $nameParts[0];
        if (isset($this->imports[$firstNamePart])) {
            if (count($nameParts) === 1) {
                return $this->imports[$firstNamePart];
            }

            array_shift($nameParts);

            return sprintf('%s\\%s', $this->imports[$firstNamePart], implode('\\', $nameParts));
        }

        if ($this->namespace !== null) {
            return sprintf('%s\\%s', $this->namespace, $name);
        }

        return $name;
    }

    /**
     * @return array{
     *     file_path: string,
     *     class: string,
     *     has_parent_class_or_interface: bool,
     *     has_api_tag: bool,
     *     used_attributes: string[],
     *     imports: array<string, string>
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'file_path' => $this->filePath,
            'namespace' => $this->namespace,
            'class' => $this->className,
            'has_parent_class_or_interface' => $this->hasParentClassOrInterface,
            'has_api_tag' => $this->hasApiTag,
            'used_attributes' => $this->usedAttributes,
            'imports' => $this->imports,
        ];
    }
}
