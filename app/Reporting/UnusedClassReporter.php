<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Reporting;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use TomasVotruba\ClassLeak\ValueObject\FileWithClass;

final class UnusedClassReporter
{
    /**
     * @readonly
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
    }

    /**
     * @param FileWithClass[] $unusedFilesWithClasses
     * @param FileWithClass[] $existingFilesWithClasses
     * @return Command::*
     */
    public function reportResult(array $unusedFilesWithClasses, array $existingFilesWithClasses): int
    {
        $this->symfonyStyle->newLine(2);

        if ($unusedFilesWithClasses === []) {
            $successMessage = sprintf('All the %d services are used. Great job!', count($existingFilesWithClasses));
            $this->symfonyStyle->success($successMessage);
            return Command::SUCCESS;
        }

        foreach ($unusedFilesWithClasses as $unusedFileWithClass) {
            $this->symfonyStyle->writeln(' * ' . $unusedFileWithClass->getClassName());
            $this->symfonyStyle->writeln($unusedFileWithClass->getFilePath());
            $this->symfonyStyle->newLine();
        }

        $successMessage = sprintf(
            'Found %d unused classes. Check them, remove them or correct the command.',
            count($unusedFilesWithClasses)
        );

        $this->symfonyStyle->error($successMessage);

        return Command::FAILURE;
    }
}
