<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Commands;

use Closure;
use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Output\OutputPrinter;
use Entropy\Console\Output\ProgressBar;
use TomasVotruba\ClassLeak\ConstructorParamTypeResolver;
use TomasVotruba\ClassLeak\Filtering\PossiblyUnusedClassesFilter;
use TomasVotruba\ClassLeak\Finder\ClassNamesFinder;
use TomasVotruba\ClassLeak\Finder\PhpFilesFinder;
use TomasVotruba\ClassLeak\Reporting\UnusedClassesResultFactory;
use TomasVotruba\ClassLeak\Reporting\UnusedClassReporter;
use TomasVotruba\ClassLeak\UseImportsResolver;

final class CheckCommand implements CommandInterface
{
    private ClassNamesFinder $classNamesFinder;

    private UseImportsResolver $useImportsResolver;

    private ConstructorParamTypeResolver $constructorParamTypeResolver;

    private PossiblyUnusedClassesFilter $possiblyUnusedClassesFilter;

    private UnusedClassReporter $unusedClassReporter;

    private OutputPrinter $outputPrinter;

    private PhpFilesFinder $phpFilesFinder;

    private UnusedClassesResultFactory $unusedClassesResultFactory;

    private ProgressBar $progressBar;

    public function __construct(
        ClassNamesFinder $classNamesFinder,
        UseImportsResolver $useImportsResolver,
        ConstructorParamTypeResolver $constructorParamTypeResolver,
        PossiblyUnusedClassesFilter $possiblyUnusedClassesFilter,
        UnusedClassReporter $unusedClassReporter,
        OutputPrinter $outputPrinter,
        PhpFilesFinder $phpFilesFinder,
        UnusedClassesResultFactory $unusedClassesResultFactory,
        ProgressBar $progressBar
    ) {
        $this->classNamesFinder = $classNamesFinder;
        $this->useImportsResolver = $useImportsResolver;
        $this->constructorParamTypeResolver = $constructorParamTypeResolver;
        $this->possiblyUnusedClassesFilter = $possiblyUnusedClassesFilter;
        $this->unusedClassReporter = $unusedClassReporter;
        $this->outputPrinter = $outputPrinter;
        $this->phpFilesFinder = $phpFilesFinder;
        $this->unusedClassesResultFactory = $unusedClassesResultFactory;
        $this->progressBar = $progressBar;
    }

    public function getName(): string
    {
        return 'check';
    }

    public function getDescription(): string
    {
        return 'Check classes that are not used in any config and in the code';
    }

    /**
     * @api called by entropy console via reflection
     *
     * @option $skipType
     * @option $skipSuffix
     * @option $skipPath
     * @option $skipAttribute
     * @option $fileExtension
     *
     * @param string[] $paths Files and directories to analyze
     * @param string[] $skipType Class types that should be skipped
     * @param string[] $skipSuffix Class suffix that should be skipped
     * @param string[] $skipPath Paths to skip (real path or just directory name)
     * @param string[] $skipAttribute Class attribute that should be skipped
     * @param bool $includeEntities Include Doctrine ORM and ODM entities (skipped by default)
     * @param string[] $fileExtension File extensions to check
     * @param bool $json Output as JSON
     * @param bool $ansi Kept for backward compatibility, colored output is always on
     * @param bool $blink Run the fast Go port instead of the PHP engine
     */
    public function run(
        array $paths,
        array $skipType = [],
        array $skipSuffix = [],
        array $skipPath = [],
        array $skipAttribute = [],
        bool $includeEntities = false,
        array $fileExtension = ['php'],
        bool $json = false,
        bool $ansi = false,
        bool $blink = false
    ): int {
        if ($blink) {
            return $this->delegateToGo(
                $paths,
                $skipType,
                $skipSuffix,
                $skipPath,
                $skipAttribute,
                $fileExtension,
                $includeEntities,
                $json
            );
        }

        // we have to look for usage in every path
        $allFilePaths = $this->phpFilesFinder->findPhpFiles($paths, $fileExtension, []);

        // but we only want to check the files that are not in the skipped paths
        $phpFilePaths = $this->phpFilesFinder->findPhpFiles($paths, $fileExtension, $skipPath);

        $progressCallback = null;
        if (! $json) {
            $this->outputPrinter->title('1. Finding used classes');
            $progressCallback = $this->createProgressCallback(count($allFilePaths));
        }

        $usedNames = $this->resolveUsedClassNames($allFilePaths, $progressCallback);
        $constructorInjectedNames = $this->resolveConstructorInjectedNames($allFilePaths);

        if (! $json) {
            $this->progressBar->finish();
            $this->outputPrinter->newline();
        }

        $progressCallback = null;
        if (! $json) {
            $this->outputPrinter->title('2. Extracting existing files with classes');
            $progressCallback = $this->createProgressCallback(count($phpFilePaths));
        }

        $existingFilesWithClasses = $this->classNamesFinder->resolveClassNamesToCheck($phpFilePaths, $progressCallback);

        if (! $json) {
            $this->progressBar->finish();
            $this->outputPrinter->newline();
        }

        $possiblyUnusedFilesWithClasses = $this->possiblyUnusedClassesFilter->filter(
            $existingFilesWithClasses,
            $usedNames,
            $skipType,
            $skipSuffix,
            $skipAttribute,
            $includeEntities,
            $constructorInjectedNames,
        );

        $unusedClassesResult = $this->unusedClassesResultFactory->create($possiblyUnusedFilesWithClasses);
        if (! $json) {
            $this->outputPrinter->newline();
        }

        return $this->unusedClassReporter->reportResult($unusedClassesResult, $json);
    }

    /**
     * @param string[] $phpFilePaths
     * @return string[]
     */
    private function resolveUsedClassNames(array $phpFilePaths, ?Closure $progressCallback): array
    {
        $usedNames = [];

        foreach ($phpFilePaths as $phpFilePath) {
            $currentUsedNames = $this->useImportsResolver->resolve($phpFilePath);
            $usedNames = [...$usedNames, ...$currentUsedNames];

            if ($progressCallback instanceof Closure) {
                $progressCallback->__invoke();
            }
        }

        $usedNames = array_unique($usedNames);
        sort($usedNames);

        return $usedNames;
    }

    /**
     * @param string[] $phpFilePaths
     * @return string[] types injected as constructor parameters, used to keep classes wired by their interface
     */
    private function resolveConstructorInjectedNames(array $phpFilePaths): array
    {
        $constructorInjectedNames = [];

        foreach ($phpFilePaths as $phpFilePath) {
            $currentNames = $this->constructorParamTypeResolver->resolve($phpFilePath);
            $constructorInjectedNames = [...$constructorInjectedNames, ...$currentNames];
        }

        return array_unique($constructorInjectedNames);
    }

    private function createProgressCallback(int $max): Closure
    {
        $this->progressBar->start($max);

        return function (): void {
            $this->progressBar->advance();
        };
    }

    /**
     * @param string[] $paths
     * @param string[] $skipType
     * @param string[] $skipSuffix
     * @param string[] $skipPath
     * @param string[] $skipAttribute
     * @param string[] $fileExtension
     */
    private function delegateToGo(
        array $paths,
        array $skipType,
        array $skipSuffix,
        array $skipPath,
        array $skipAttribute,
        array $fileExtension,
        bool $includeEntities,
        bool $json
    ): int {
        $launcher = __DIR__ . '/../../bin/class-leak-go';
        if (! is_file($launcher)) {
            fwrite(STDERR, 'class-leak --blink: Go launcher not found at ' . $launcher . PHP_EOL);
            return 1;
        }

        $arguments = ['check'];
        $arguments = [...$arguments, ...$paths];

        foreach ($skipType as $value) {
            $arguments[] = '--skip-type';
            $arguments[] = $value;
        }

        foreach ($skipSuffix as $value) {
            $arguments[] = '--skip-suffix';
            $arguments[] = $value;
        }

        foreach ($skipPath as $value) {
            $arguments[] = '--skip-path';
            $arguments[] = $value;
        }

        foreach ($skipAttribute as $value) {
            $arguments[] = '--skip-attribute';
            $arguments[] = $value;
        }

        foreach ($fileExtension as $value) {
            $arguments[] = '--file-extension';
            $arguments[] = $value;
        }

        if ($includeEntities) {
            $arguments[] = '--include-entities';
        }

        if ($json) {
            $arguments[] = '--json';
        }

        $command = escapeshellarg($launcher);
        foreach ($arguments as $argument) {
            $command .= ' ' . escapeshellarg($argument);
        }

        $exitCode = 0;
        passthru($command, $exitCode);

        return $exitCode;
    }
}
