<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TomasVotruba\ClassLeak\Filtering\PossiblyUnusedClassesFilter;
use TomasVotruba\ClassLeak\Finder\ClassNamesFinder;
use TomasVotruba\ClassLeak\Finder\PhpFilesFinder;
use TomasVotruba\ClassLeak\Reporting\UnusedClassesResultFactory;
use TomasVotruba\ClassLeak\Reporting\UnusedClassReporter;
use TomasVotruba\ClassLeak\UseImportsResolver;

final class CheckCommand extends Command
{
    public function __construct(
        private readonly ClassNamesFinder $classNamesFinder,
        private readonly UseImportsResolver $useImportsResolver,
        private readonly PossiblyUnusedClassesFilter $possiblyUnusedClassesFilter,
        private readonly UnusedClassReporter $unusedClassReporter,
        private readonly SymfonyStyle $symfonyStyle,
        private readonly PhpFilesFinder $phpFilesFinder,
        private readonly UnusedClassesResultFactory $unusedClassesResultFactory,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('check');
        $this->setDescription('Check classes that are not used in any config and in the code');

        $this->addArgument(
            'paths',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Files and directories to analyze'
        );
        $this->addOption(
            'skip-type',
            null,
            InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
            'Class types that should be skipped'
        );

        $this->addOption(
            'skip-suffix',
            null,
            InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
            'Class suffix that should be skipped'
        );

        $this->addOption(
            'skip-path',
            null,
            InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
            'Paths to skip (real path or just directory name)'
        );

        $this->addOption(
            'skip-attribute',
            null,
            InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
            'Class attribute that should be skipped'
        );

        $this->addOption(
            'include-entities',
            null,
            InputOption::VALUE_NONE,
            'Include Doctrine ORM and ODM entities (skipped by default)',
        );

        $this->addOption(
            'file-extension',
            null,
            InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
            'File extensions to check',
            ['php']
        );

        $this->addOption('json', null, InputOption::VALUE_NONE, 'Output as JSON');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string[] $paths */
        $paths = (array) $input->getArgument('paths');

        $shouldIncludeEntities = (bool) $input->getOption('include-entities');

        /** @var string[] $typesToSkip */
        $typesToSkip = (array) $input->getOption('skip-type');

        /** @var string[] $suffixesToSkip */
        $suffixesToSkip = (array) $input->getOption('skip-suffix');

        /** @var string[] $attributesToSkip */
        $attributesToSkip = (array) $input->getOption('skip-attribute');

        /** @var string[] $pathsToSkip */
        $pathsToSkip = (array) $input->getOption('skip-path');

        $isJson = (bool) $input->getOption('json');

        /** @var string[] $fileExtensions */
        $fileExtensions = (array) $input->getOption('file-extension');

        $phpFilePaths = $this->phpFilesFinder->findPhpFiles($paths, $fileExtensions, $pathsToSkip);

        $progressBar = null;
        if (! $isJson) {
            $this->symfonyStyle->title('1. Finding used classes');
            $progressBar = $this->symfonyStyle->createProgressBar(count($phpFilePaths));
        }

        $usedNames = $this->resolveUsedClassNames($phpFilePaths, $progressBar);

        $this->symfonyStyle->newLine(2);

        $progressBar = null;
        if (! $isJson) {
            $this->symfonyStyle->title('2. Extracting existing files with classes');
            $progressBar = $this->symfonyStyle->createProgressBar(count($phpFilePaths));
        }

        $existingFilesWithClasses = $this->classNamesFinder->resolveClassNamesToCheck($phpFilePaths, $progressBar);

        $this->symfonyStyle->newLine(2);

        $possiblyUnusedFilesWithClasses = $this->possiblyUnusedClassesFilter->filter(
            $existingFilesWithClasses,
            $usedNames,
            $typesToSkip,
            $suffixesToSkip,
            $attributesToSkip,
            $shouldIncludeEntities,
        );

        $unusedClassesResult = $this->unusedClassesResultFactory->create($possiblyUnusedFilesWithClasses);
        $this->symfonyStyle->newLine();

        return $this->unusedClassReporter->reportResult($unusedClassesResult, $isJson);
    }

    /**
     * @param string[] $phpFilePaths
     * @return string[]
     */
    private function resolveUsedClassNames(array $phpFilePaths, ?ProgressBar $progressBar): array
    {
        $usedNames = [];

        foreach ($phpFilePaths as $phpFilePath) {
            $currentUsedNames = $this->useImportsResolver->resolve($phpFilePath);
            $usedNames = [...$usedNames, ...$currentUsedNames];

            $progressBar?->advance();
        }

        $usedNames = array_unique($usedNames);
        sort($usedNames);

        return $usedNames;
    }
}
