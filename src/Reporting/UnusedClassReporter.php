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
    /**
     * @return Command::*
     */
    public function reportResult(SymfonyStyle $symfonyStyle, UnusedClassesResult $unusedClassesResult, int $classCount, bool $isJson): int
    {
        if ($isJson) {
            $jsonResult = [
                'unused_class_count' => $unusedClassesResult->getCount(),
                'unused_parent_less_classes' => $unusedClassesResult->getParentLessFileWithClasses(),
                'unused_classes_with_parents' => $unusedClassesResult->getWithParentsFileWithClasses(),
            ];

            $symfonyStyle->writeln(Json::encode($jsonResult, Json::PRETTY));

            return Command::SUCCESS;
        }

        $symfonyStyle->newLine(2);

        if ($unusedClassesResult->getCount() === 0) {
            $symfonyStyle->success(sprintf('All the %d services are used. Great job!', $classCount));
            return Command::SUCCESS;
        }

        // separate with and without parent, as first one can be removed more easily
        if ($unusedClassesResult->getWithParentsFileWithClasses() !== []) {
            $symfonyStyle->title('Classes with a parent/interface - possibly used by type');

            $this->reportFileWithClasses($symfonyStyle, $unusedClassesResult->getWithParentsFileWithClasses());
        }

        if ($unusedClassesResult->getParentLessFileWithClasses() !== []) {
            $symfonyStyle->newLine();
            $symfonyStyle->title('Classes without any parent/interface - easier to remove');

            $this->reportFileWithClasses($symfonyStyle, $unusedClassesResult->getParentLessFileWithClasses());
        }

        $symfonyStyle->newLine();
        $symfonyStyle->error(sprintf(
            'Found %d unused classes. Check and remove them or skip them using "--skip-type" option',
            $unusedClassesResult->getCount()
        ));

        return Command::FAILURE;
    }

    /**
     * @param FileWithClass[] $fileWithClasses
     */
    private function reportFileWithClasses(SymfonyStyle $symfonyStyle, array $fileWithClasses): void
    {
        foreach ($fileWithClasses as $fileWithClass) {
            $symfonyStyle->writeln(' * ' . $fileWithClass->getClassName());
            $symfonyStyle->writeln($fileWithClass->getFilePath());
            $symfonyStyle->newLine();
        }
    }
}
