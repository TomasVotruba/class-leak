<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Reporting;

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
    public function reportResult(UnusedClassesResult $unusedClassesResult): int
    {
        $this->symfonyStyle->newLine(2);

        if ($unusedClassesResult->getCount() === 0) {
            $this->symfonyStyle->success('All services are used. Great job!');
            return Command::SUCCESS;
        }

        // separate with and without parent, as first one can be removed more easily
        if ($unusedClassesResult->getWithParentsFileWithClasses() !== []) {
            $this->printLineWIthClasses(
                'Classes with a parent/interface',
                $unusedClassesResult->getWithParentsFileWithClasses()
            );
        }

        if ($unusedClassesResult->getParentLessFileWithClasses() !== []) {
            $this->printLineWIthClasses(
                'Classes without any parent/interface - easier to remove',
                $unusedClassesResult->getParentLessFileWithClasses()
            );
        }

        if ($unusedClassesResult->getTraits() !== []) {
            $this->printLineWIthClasses('Unused traits - the easiest to remove', $unusedClassesResult->getTraits());
        }

        $this->symfonyStyle->newLine();
        $this->symfonyStyle->error(sprintf(
            'Found %d unused classes. Remove them or skip them using "--skip-type" option',
            $unusedClassesResult->getCount()
        ));

        return Command::FAILURE;
    }

    /**
     * @param FileWithClass[] $fileWithClasses
     */
    private function printLineWIthClasses(string $title, array $fileWithClasses): void
    {
        $this->symfonyStyle->newLine();
        $this->symfonyStyle->section($title);

        foreach ($fileWithClasses as $fileWithClass) {
            $this->symfonyStyle->writeln($fileWithClass->getFilePath());
        }
    }
}
