<?php

declare (strict_types=1);
namespace TomasVotruba\ClassLeak\Finder;

use ClassLeak202412\Symfony\Component\Console\Style\SymfonyStyle;
use TomasVotruba\ClassLeak\ClassNameResolver;
use TomasVotruba\ClassLeak\ValueObject\ClassNames;
use TomasVotruba\ClassLeak\ValueObject\FileWithClass;
final class ClassNamesFinder
{
    /**
     * @readonly
     * @var \TomasVotruba\ClassLeak\ClassNameResolver
     */
    private $classNameResolver;
    /**
     * @readonly
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    public function __construct(ClassNameResolver $classNameResolver, SymfonyStyle $symfonyStyle)
    {
        $this->classNameResolver = $classNameResolver;
        $this->symfonyStyle = $symfonyStyle;
    }
    /**
     * @param string[] $filePaths
     * @return FileWithClass[]
     */
    public function resolveClassNamesToCheck(array $filePaths) : array
    {
        $progressBar = $this->symfonyStyle->createProgressBar(\count($filePaths));
        $filesWithClasses = [];
        foreach ($filePaths as $filePath) {
            $progressBar->advance();
            $classNames = $this->classNameResolver->resolveFromFilePath($filePath);
            if (!$classNames instanceof ClassNames) {
                continue;
            }
            $filesWithClasses[] = new FileWithClass($filePath, $classNames->getClassName(), $classNames->hasParentClassOrInterface(), $classNames->getAttributes());
        }
        return $filesWithClasses;
    }
}
