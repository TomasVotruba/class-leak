<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use TomasVotruba\ClassLeak\Filtering\PossiblyUnusedClassesFilter;
use TomasVotruba\ClassLeak\Finder\ClassNamesFinder;
use TomasVotruba\ClassLeak\Finder\PhpFilesFinder;
use TomasVotruba\ClassLeak\Reporting\UnusedClassReporter;
use TomasVotruba\ClassLeak\UseImportsResolver;
use TomasVotruba\ClassLeak\ValueObject\Option;

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

    /**
     * @var string
     * @see https://laravel.com/docs/10.x/artisan#command-structure
     */
    protected $signature = 'check {paths*}';

    /**
     * @var string
     */
    protected $description = 'Check classes that are not used in any config and in the code';

    protected function handle(): int
    {
        $paths = $this->argument('paths');
        $phpFilePaths = $this->phpFilesFinder->findPhpFiles($paths);

        $this->symfonyStyle->progressStart(count($phpFilePaths));

        $usedNames = [];
        foreach ($phpFilePaths as $phpFilePath) {
            $currentUsedNames = $this->useImportsResolver->resolve($phpFilePath);
            $usedNames = array_merge($usedNames, $currentUsedNames);

            $this->symfonyStyle->progressAdvance();
        }

        $usedNames = array_unique($usedNames);
        sort($usedNames);

        $existingFilesWithClasses = $this->classNamesFinder->resolveClassNamesToCheck($phpFilePaths);

        $possiblyUnusedFilesWithClasses = $this->possiblyUnusedClassesFilter->filter(
            $existingFilesWithClasses,
            $usedNames
        );

        return $this->unusedClassReporter->reportResult($possiblyUnusedFilesWithClasses, $existingFilesWithClasses);
    }
}
