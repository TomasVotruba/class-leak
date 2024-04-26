<?php

declare (strict_types=1);
namespace TomasVotruba\ClassLeak\ValueObject;

final class UnusedClassesResult
{
    /**
     * @var FileWithClass[]
     * @readonly
     */
    private $parentLessFileWithClasses;
    /**
     * @var FileWithClass[]
     * @readonly
     */
    private $withParentsFileWithClasses;
    /**
     * @param FileWithClass[] $withParentsFileWithClasses
     * @param FileWithClass[] $parentLessFileWithClasses
     */
    public function __construct(array $parentLessFileWithClasses, array $withParentsFileWithClasses)
    {
        $this->parentLessFileWithClasses = $parentLessFileWithClasses;
        $this->withParentsFileWithClasses = $withParentsFileWithClasses;
    }
    /**
     * @return FileWithClass[]
     */
    public function getParentLessFileWithClasses() : array
    {
        return $this->parentLessFileWithClasses;
    }
    /**
     * @return FileWithClass[]
     */
    public function getWithParentsFileWithClasses() : array
    {
        return $this->withParentsFileWithClasses;
    }
    public function getCount() : int
    {
        return \count($this->parentLessFileWithClasses) + \count($this->withParentsFileWithClasses);
    }
}
