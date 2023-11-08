<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Commands;

use Closure;
use Symfony\Component\Console\Command\Command;
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
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string[] $paths */
        $paths = (array) $input->getArgument('paths');

        /** @var string[] $typesToSkip */
        $typesToSkip = (array) $input->getOption('skip-type');

        /** @var string[] $suffixesToSkip */
        $suffixesToSkip = (array) $input->getOption('skip-suffix');

        $phpFilePaths = $this->phpFilesFinder->findPhpFiles($paths);

        $this->symfonyStyle->progressStart(count($phpFilePaths));
        $this->symfonyStyle->newLine();

        $usedNames = $this->resolveUsedClassNames($phpFilePaths, function (): void {
            $this->symfonyStyle->progressAdvance();
        });

        $existingFilesWithClasses = $this->classNamesFinder->resolveClassNamesToCheck($phpFilePaths);

        $possiblyUnusedFilesWithClasses = $this->possiblyUnusedClassesFilter->filter(
            $existingFilesWithClasses,
            $usedNames,
            $typesToSkip,
            $suffixesToSkip
        );

        $unusedClassesResult = $this->unusedClassesResultFactory->create($possiblyUnusedFilesWithClasses);

        return $this->unusedClassReporter->reportResult($unusedClassesResult, count($existingFilesWithClasses));
    }

    /**
     * @param string[] $phpFilePaths
     * @return string[]
     */
    private function resolveUsedClassNames(array $phpFilePaths, Closure $progressClosure): array
    {
        $usedNames = [];

        foreach ($phpFilePaths as $phpFilePath) {
            $currentUsedNames = $this->useImportsResolver->resolve($phpFilePath);
            $usedNames = [...$usedNames, ...$currentUsedNames];

            $progressClosure();
        }

        $usedNames = array_unique($usedNames);
        sort($usedNames);

        return $usedNames;
    }
}
