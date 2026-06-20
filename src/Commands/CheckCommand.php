<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Commands;

use Closure;
use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Output\OutputPrinter;
use Entropy\Console\Output\ProgressBar;
use TomasVotruba\ClassLeak\Filtering\PossiblyUnusedClassesFilter;
use TomasVotruba\ClassLeak\Finder\ClassNamesFinder;
use TomasVotruba\ClassLeak\Finder\PhpFilesFinder;
use TomasVotruba\ClassLeak\Reporting\UnusedClassesResultFactory;
use TomasVotruba\ClassLeak\Reporting\UnusedClassReporter;
use TomasVotruba\ClassLeak\UseImportsResolver;

final readonly class CheckCommand implements CommandInterface
{
    public function __construct(
        private ClassNamesFinder $classNamesFinder,
        private UseImportsResolver $useImportsResolver,
        private PossiblyUnusedClassesFilter $possiblyUnusedClassesFilter,
        private UnusedClassReporter $unusedClassReporter,
        private OutputPrinter $outputPrinter,
        private PhpFilesFinder $phpFilesFinder,
        private UnusedClassesResultFactory $unusedClassesResultFactory,
        private ProgressBar $progressBar,
    ) {
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
    ): int {
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

        if (! $json) {
            $this->progressBar->finish();
        }

        $this->outputPrinter->newline();

        $progressCallback = null;
        if (! $json) {
            $this->outputPrinter->title('2. Extracting existing files with classes');
            $progressCallback = $this->createProgressCallback(count($phpFilePaths));
        }

        $existingFilesWithClasses = $this->classNamesFinder->resolveClassNamesToCheck($phpFilePaths, $progressCallback);

        if (! $json) {
            $this->progressBar->finish();
        }

        $this->outputPrinter->newline();

        $possiblyUnusedFilesWithClasses = $this->possiblyUnusedClassesFilter->filter(
            $existingFilesWithClasses,
            $usedNames,
            $skipType,
            $skipSuffix,
            $skipAttribute,
            $includeEntities,
        );

        $unusedClassesResult = $this->unusedClassesResultFactory->create($possiblyUnusedFilesWithClasses);
        $this->outputPrinter->newline();

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

            $progressCallback?->__invoke();
        }

        $usedNames = array_unique($usedNames);
        sort($usedNames);

        return $usedNames;
    }

    private function createProgressCallback(int $max): Closure
    {
        $this->progressBar->start($max);

        return function (): void {
            $this->progressBar->advance();
        };
    }
}
