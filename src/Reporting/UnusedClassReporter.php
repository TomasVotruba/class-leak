<?php

declare (strict_types=1);
namespace TomasVotruba\ClassLeak\Reporting;

use ClassLeak202406\Nette\Utils\Json;
use ClassLeak202406\Symfony\Component\Console\Command\Command;
use ClassLeak202406\Symfony\Component\Console\Style\SymfonyStyle;
use TomasVotruba\ClassLeak\ValueObject\FileWithClass;
use TomasVotruba\ClassLeak\ValueObject\UnusedClassesResult;
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
     * @return Command::*
     */
    public function reportResult(UnusedClassesResult $unusedClassesResult, int $classCount, bool $isJson) : int
    {
        if ($isJson) {
            $jsonResult = ['unused_class_count' => $unusedClassesResult->getCount(), 'unused_parent_less_classes' => $unusedClassesResult->getParentLessFileWithClasses(), 'unused_classes_with_parents' => $unusedClassesResult->getWithParentsFileWithClasses()];
            $this->symfonyStyle->writeln(Json::encode($jsonResult, Json::PRETTY));
            return Command::SUCCESS;
        }
        $this->symfonyStyle->newLine(2);
        if ($unusedClassesResult->getCount() === 0) {
            $this->symfonyStyle->success(\sprintf('All the %d services are used. Great job!', $classCount));
            return Command::SUCCESS;
        }
        // separate with and without parent, as first one can be removed more easily
        if ($unusedClassesResult->getWithParentsFileWithClasses() !== []) {
            $this->symfonyStyle->title('Classes with a parent/interface - possibly used by type');
            $this->reportFileWithClasses($unusedClassesResult->getWithParentsFileWithClasses());
        }
        if ($unusedClassesResult->getParentLessFileWithClasses() !== []) {
            $this->symfonyStyle->newLine();
            $this->symfonyStyle->title('Classes without any parent/interface - easier to remove');
            $this->reportFileWithClasses($unusedClassesResult->getParentLessFileWithClasses());
        }
        $this->symfonyStyle->newLine();
        $this->symfonyStyle->error(\sprintf('Found %d unused classes. Check and remove them or skip them using "--skip-type" option', $unusedClassesResult->getCount()));
        return Command::FAILURE;
    }
    /**
     * @param FileWithClass[] $fileWithClasses
     */
    private function reportFileWithClasses(array $fileWithClasses) : void
    {
        foreach ($fileWithClasses as $fileWithClass) {
            $this->symfonyStyle->writeln(' * ' . $fileWithClass->getClassName());
            $this->symfonyStyle->writeln($fileWithClass->getFilePath());
            $this->symfonyStyle->newLine();
        }
    }
}
