<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Reporting;

use Nette\Utils\Json;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use TomasVotruba\ClassLeak\ValueObject\FileWithClass;
use TomasVotruba\ClassLeak\ValueObject\UnusedClassesResult;

final readonly class UnusedClassReporter
{
    public function __construct(
        private SymfonyStyle $symfonyStyle
    ) {
    }

    /**
     * @return Command::*
     */
    public function reportResult(UnusedClassesResult $unusedClassesResult, bool $isJson): int
    {
        if ($isJson) {
            $jsonResult = [
                'unused_class_count' => $unusedClassesResult->getCount(),
                'unused_parent_less_classes' => $unusedClassesResult->getParentLessFileWithClasses(),
                'unused_classes_with_parents' => $unusedClassesResult->getWithParentsFileWithClasses(),
            ];

            $this->symfonyStyle->writeln(Json::encode($jsonResult, Json::PRETTY));

            return Command::SUCCESS;
        }

        $this->symfonyStyle->newLine(2);

        if ($unusedClassesResult->getCount() === 0) {
            $this->symfonyStyle->success('All services are used. Great job!');
            return Command::SUCCESS;
        }

        // separate with and without parent, as first one can be removed more easily
        if ($unusedClassesResult->getWithParentsFileWithClasses() !== []) {
            $this->symfonyStyle->section('Classes with a parent/interface - possibly used by type');

            $this->reportFileWithClasses($unusedClassesResult->getWithParentsFileWithClasses());
        }

        if ($unusedClassesResult->getParentLessFileWithClasses() !== []) {
            $this->symfonyStyle->newLine();
            $this->symfonyStyle->section('Classes without any parent/interface - easier to remove');

            $this->reportFileWithClasses($unusedClassesResult->getParentLessFileWithClasses());
        }

        // @todo traits - the easier to remove as hardly used anywhere
        if ($unusedClassesResult->getTraits() !== []) {
            $this->symfonyStyle->newLine();
            $this->symfonyStyle->section('Traits without any use - the easiest to remove');

            $this->reportFileWithClasses($unusedClassesResult->getTraits());
        }

        $this->symfonyStyle->newLine();
        $this->symfonyStyle->error(sprintf(
            'Found %d unused classes. Check and remove them or skip them using "--skip-type" option',
            $unusedClassesResult->getCount()
        ));

        return Command::FAILURE;
    }

    /**
     * @param FileWithClass[] $fileWithClasses
     */
    private function reportFileWithClasses(array $fileWithClasses): void
    {
        foreach ($fileWithClasses as $fileWithClass) {
            $this->symfonyStyle->writeln(' * ' . $fileWithClass->getClassName());
            $this->symfonyStyle->writeln($fileWithClass->getFilePath());
            $this->symfonyStyle->newLine();
        }
    }
}
