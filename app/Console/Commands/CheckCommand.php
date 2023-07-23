<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Console\Commands;

use Closure;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;
use TomasVotruba\ClassLeak\Filtering\PossiblyUnusedClassesFilter;
use TomasVotruba\ClassLeak\Finder\ClassNamesFinder;
use TomasVotruba\ClassLeak\Finder\PhpFilesFinder;
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
            'configuration',
            'c',
            InputOption::VALUE_REQUIRED,
            'Configuration file name'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string[] $paths */
        $paths = (array) $input->getArgument('paths');

        $typesToSkip = self::resolveTypesToSkip($input);

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
            $typesToSkip
        );

        return $this->unusedClassReporter->reportResult(
            $possiblyUnusedFilesWithClasses,
            $existingFilesWithClasses
        );
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
            $usedNames = array_merge($usedNames, $currentUsedNames);

            $progressClosure();
        }

        $usedNames = array_unique($usedNames);
        sort($usedNames);

        return $usedNames;
    }

    /**
     * @param InputInterface $input
     * @return string[]
     */
    private static function resolveTypesToSkip(InputInterface $input): array
    {
        /** @var string[] $typesToSkip */
        $typesToSkip = [];

        if ($input->getOption('configuration') !== null) {
            $config = Yaml::parseFile($input->getOption('configuration'));
            $typesToSkip = $config['typesToSkip'] ?? [];
        }

        if (count($input->getOption('skip-type')) > 0) {
            $typesToSkip = (array)$input->getOption('skip-type');
        }

        return $typesToSkip;
    }
}
