<?php

declare (strict_types=1);
namespace TomasVotruba\ClassLeak\Console\Commands;

use Closure;
use ClassLeak202308\Symfony\Component\Console\Command\Command;
use ClassLeak202308\Symfony\Component\Console\Input\InputArgument;
use ClassLeak202308\Symfony\Component\Console\Input\InputInterface;
use ClassLeak202308\Symfony\Component\Console\Input\InputOption;
use ClassLeak202308\Symfony\Component\Console\Output\OutputInterface;
use ClassLeak202308\Symfony\Component\Console\Style\SymfonyStyle;
use TomasVotruba\ClassLeak\Filtering\PossiblyUnusedClassesFilter;
use TomasVotruba\ClassLeak\Finder\ClassNamesFinder;
use TomasVotruba\ClassLeak\Finder\PhpFilesFinder;
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
    public function __construct(ClassNamesFinder $classNamesFinder, UseImportsResolver $useImportsResolver, PossiblyUnusedClassesFilter $possiblyUnusedClassesFilter, UnusedClassReporter $unusedClassReporter, SymfonyStyle $symfonyStyle, PhpFilesFinder $phpFilesFinder)
    {
        $this->classNamesFinder = $classNamesFinder;
        $this->useImportsResolver = $useImportsResolver;
        $this->possiblyUnusedClassesFilter = $possiblyUnusedClassesFilter;
        $this->unusedClassReporter = $unusedClassReporter;
        $this->symfonyStyle = $symfonyStyle;
        $this->phpFilesFinder = $phpFilesFinder;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('check');
        $this->setDescription('Check classes that are not used in any config and in the code');
        $this->addArgument('paths', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Files and directories to analyze');
        $this->addOption('skip-type', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'Class types that should be skipped');
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        /** @var string[] $paths */
        $paths = (array) $input->getArgument('paths');
        /** @var string[] $typesToSkip */
        $typesToSkip = (array) $input->getOption('skip-type');
        $phpFilePaths = $this->phpFilesFinder->findPhpFiles($paths);
        $this->symfonyStyle->progressStart(\count($phpFilePaths));
        $this->symfonyStyle->newLine();
        $usedNames = $this->resolveUsedClassNames($phpFilePaths, function () : void {
            $this->symfonyStyle->progressAdvance();
        });
        $existingFilesWithClasses = $this->classNamesFinder->resolveClassNamesToCheck($phpFilePaths);
        $possiblyUnusedFilesWithClasses = $this->possiblyUnusedClassesFilter->filter($existingFilesWithClasses, $usedNames, $typesToSkip);
        return $this->unusedClassReporter->reportResult($possiblyUnusedFilesWithClasses, $existingFilesWithClasses);
    }
    /**
     * @param string[] $phpFilePaths
     * @return string[]
     */
    private function resolveUsedClassNames(array $phpFilePaths, Closure $progressClosure) : array
    {
        $usedNames = [];
        foreach ($phpFilePaths as $phpFilePath) {
            $currentUsedNames = $this->useImportsResolver->resolve($phpFilePath);
            $usedNames = \array_merge($usedNames, $currentUsedNames);
            $progressClosure();
        }
        $usedNames = \array_unique($usedNames);
        \sort($usedNames);
        return $usedNames;
    }
}
