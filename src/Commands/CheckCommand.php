<?php

declare (strict_types=1);
namespace TomasVotruba\ClassLeak\Commands;

use Closure;
use ClassLeak202407\Symfony\Component\Console\Command\Command;
use ClassLeak202407\Symfony\Component\Console\Input\InputArgument;
use ClassLeak202407\Symfony\Component\Console\Input\InputInterface;
use ClassLeak202407\Symfony\Component\Console\Input\InputOption;
use ClassLeak202407\Symfony\Component\Console\Output\OutputInterface;
use ClassLeak202407\Symfony\Component\Console\Style\SymfonyStyle;
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
        $this->addOption('skip-attribute', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'Class attribute that should be skipped');
        $this->addOption('file-extension', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'File extensions to check', ['php']);
        $this->addOption('json', null, InputOption::VALUE_NONE, 'Output as JSON');
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        /** @var string[] $paths */
        $paths = (array) $input->getArgument('paths');
        /** @var string[] $typesToSkip */
        $typesToSkip = (array) $input->getOption('skip-type');
        /** @var string[] $suffixesToSkip */
        $suffixesToSkip = (array) $input->getOption('skip-suffix');
        /** @var string[] $attributesToSkip */
        $attributesToSkip = (array) $input->getOption('skip-attribute');
        $isJson = (bool) $input->getOption('json');
        /** @var string[] $fileExtensions */
        $fileExtensions = (array) $input->getOption('file-extension');
        $phpFilePaths = $this->phpFilesFinder->findPhpFiles($paths, $fileExtensions);
        if (!$isJson) {
            $this->symfonyStyle->progressStart(\count($phpFilePaths));
            $this->symfonyStyle->newLine();
        }
        $usedNames = $this->resolveUsedClassNames($phpFilePaths, function () use($isJson) : void {
            if ($isJson) {
                return;
            }
            $this->symfonyStyle->progressAdvance();
        });
        $existingFilesWithClasses = $this->classNamesFinder->resolveClassNamesToCheck($phpFilePaths);
        $possiblyUnusedFilesWithClasses = $this->possiblyUnusedClassesFilter->filter($existingFilesWithClasses, $usedNames, $typesToSkip, $suffixesToSkip, $attributesToSkip);
        $unusedClassesResult = $this->unusedClassesResultFactory->create($possiblyUnusedFilesWithClasses);
        return $this->unusedClassReporter->reportResult($unusedClassesResult, \count($existingFilesWithClasses), $isJson);
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
