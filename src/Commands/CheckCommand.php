<?php

declare (strict_types=1);
namespace TomasVotruba\ClassLeak\Commands;

use ClassLeak202412\Symfony\Component\Console\Command\Command;
use ClassLeak202412\Symfony\Component\Console\Input\InputArgument;
use ClassLeak202412\Symfony\Component\Console\Input\InputInterface;
use ClassLeak202412\Symfony\Component\Console\Input\InputOption;
use ClassLeak202412\Symfony\Component\Console\Output\OutputInterface;
use ClassLeak202412\Symfony\Component\Console\Style\SymfonyStyle;
use TomasVotruba\ClassLeak\Filtering\PossiblyUnusedClassesFilter;
use TomasVotruba\ClassLeak\Finder\ClassNamesFinder;
use TomasVotruba\ClassLeak\Finder\PhpFilesFinder;
use TomasVotruba\ClassLeak\Reporting\UnusedClassesResultFactory;
use TomasVotruba\ClassLeak\Reporting\UnusedClassReporter;
use TomasVotruba\ClassLeak\UseImportsResolver;
final class CheckCommand extends Command
{
    /**
     * @readonly
     * @var \TomasVotruba\ClassLeak\Finder\ClassNamesFinder
     */
    private $classNamesFinder;
    /**
     * @readonly
     * @var \TomasVotruba\ClassLeak\UseImportsResolver
     */
    private $useImportsResolver;
    /**
     * @readonly
     * @var \TomasVotruba\ClassLeak\Filtering\PossiblyUnusedClassesFilter
     */
    private $possiblyUnusedClassesFilter;
    /**
     * @readonly
     * @var \TomasVotruba\ClassLeak\Reporting\UnusedClassReporter
     */
    private $unusedClassReporter;
    /**
     * @readonly
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    /**
     * @readonly
     * @var \TomasVotruba\ClassLeak\Finder\PhpFilesFinder
     */
    private $phpFilesFinder;
    /**
     * @readonly
     * @var \TomasVotruba\ClassLeak\Reporting\UnusedClassesResultFactory
     */
    private $unusedClassesResultFactory;
    public function __construct(ClassNamesFinder $classNamesFinder, UseImportsResolver $useImportsResolver, PossiblyUnusedClassesFilter $possiblyUnusedClassesFilter, UnusedClassReporter $unusedClassReporter, SymfonyStyle $symfonyStyle, PhpFilesFinder $phpFilesFinder, UnusedClassesResultFactory $unusedClassesResultFactory)
    {
        $this->classNamesFinder = $classNamesFinder;
        $this->useImportsResolver = $useImportsResolver;
        $this->possiblyUnusedClassesFilter = $possiblyUnusedClassesFilter;
        $this->unusedClassReporter = $unusedClassReporter;
        $this->symfonyStyle = $symfonyStyle;
        $this->phpFilesFinder = $phpFilesFinder;
        $this->unusedClassesResultFactory = $unusedClassesResultFactory;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('check');
        $this->setDescription('Check classes that are not used in any config and in the code');
        $this->addArgument('paths', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Files and directories to analyze');
        $this->addOption('skip-type', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'Class types that should be skipped');
        $this->addOption('skip-suffix', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'Class suffix that should be skipped');
        $this->addOption('skip-path', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'Paths to skip (real path or just directory name)');
        $this->addOption('skip-attribute', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'Class attribute that should be skipped');
        $this->addOption('include-entities', null, InputOption::VALUE_NONE, 'Include Doctrine ORM and ODM entities (skipped by default)');
        $this->addOption('file-extension', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'File extensions to check', ['php']);
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
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
        /** @var string[] $fileExtensions */
        $fileExtensions = (array) $input->getOption('file-extension');
        $phpFilePaths = $this->phpFilesFinder->findPhpFiles($paths, $fileExtensions, $pathsToSkip);
        $this->symfonyStyle->title('1. Finding used classes');
        $usedNames = $this->resolveUsedClassNames($phpFilePaths);
        $this->symfonyStyle->newLine(2);
        $this->symfonyStyle->title('2. Extracting existing files with classes');
        $existingFilesWithClasses = $this->classNamesFinder->resolveClassNamesToCheck($phpFilePaths);
        $this->symfonyStyle->newLine(2);
        $this->symfonyStyle->title('3. Comparing found classes to their usage');
        $possiblyUnusedFilesWithClasses = $this->possiblyUnusedClassesFilter->filter($existingFilesWithClasses, $usedNames, $typesToSkip, $suffixesToSkip, $attributesToSkip, $shouldIncludeEntities);
        $unusedClassesResult = $this->unusedClassesResultFactory->create($possiblyUnusedFilesWithClasses);
        $this->symfonyStyle->newLine();
        return $this->unusedClassReporter->reportResult($unusedClassesResult);
    }
    /**
     * @param string[] $phpFilePaths
     * @return string[]
     */
    private function resolveUsedClassNames(array $phpFilePaths) : array
    {
        $progressBar = $this->symfonyStyle->createProgressBar(\count($phpFilePaths));
        $usedNames = [];
        foreach ($phpFilePaths as $phpFilePath) {
            $currentUsedNames = $this->useImportsResolver->resolve($phpFilePath);
            $usedNames = \array_merge($usedNames, $currentUsedNames);
            $progressBar->advance();
        }
        $usedNames = \array_unique($usedNames);
        \sort($usedNames);
        return $usedNames;
    }
}
