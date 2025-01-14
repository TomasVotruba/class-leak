<?php

declare (strict_types=1);
namespace TomasVotruba\ClassLeak\Reporting;

use ClassLeak202501\Nette\Utils\Json;
use ClassLeak202501\Symfony\Component\Console\Command\Command;
use ClassLeak202501\Symfony\Component\Console\Style\SymfonyStyle;
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
    public function reportResult(UnusedClassesResult $unusedClassesResult, bool $isJson) : int
    {
        if ($isJson) {
            $jsonResult = ['unused_class_count' => $unusedClassesResult->getCount(), 'unused_parent_less_classes' => $unusedClassesResult->getParentLessFileWithClasses(), 'unused_classes_with_parents' => $unusedClassesResult->getWithParentsFileWithClasses(), 'unused_traits' => $unusedClassesResult->getTraits()];
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
            $this->printLineWIthClasses('Classes with a parent/interface', $unusedClassesResult->getWithParentsFileWithClasses());
        }
        if ($unusedClassesResult->getParentLessFileWithClasses() !== []) {
            $this->printLineWIthClasses('Classes without any parent/interface - easier to remove', $unusedClassesResult->getParentLessFileWithClasses());
        }
        if ($unusedClassesResult->getTraits() !== []) {
            $this->printLineWIthClasses('Unused traits - the easiest to remove', $unusedClassesResult->getTraits());
        }
        $this->symfonyStyle->newLine();
        $this->symfonyStyle->error(\sprintf('Found %d unused classes. Remove them or skip them using "--skip-type" option', $unusedClassesResult->getCount()));
        return Command::FAILURE;
    }
    /**
     * @param FileWithClass[] $fileWithClasses
     */
    private function printLineWIthClasses(string $title, array $fileWithClasses) : void
    {
        $this->symfonyStyle->newLine();
        $this->symfonyStyle->section($title);
        foreach ($fileWithClasses as $fileWithClass) {
            $this->symfonyStyle->writeln($fileWithClass->getFilePath());
        }
    }
}
