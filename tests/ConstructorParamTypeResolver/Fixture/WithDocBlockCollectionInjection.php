<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Tests\ConstructorParamTypeResolver\Fixture;

use TomasVotruba\ClassLeak\Tests\ConstructorParamTypeResolver\Source;
use TomasVotruba\ClassLeak\Tests\ConstructorParamTypeResolver\Source\AliasedInjectedInterface as AliasedAlias;
use TomasVotruba\ClassLeak\Tests\ConstructorParamTypeResolver\Source\ArrayInjectedInterface;
use TomasVotruba\ClassLeak\Tests\ConstructorParamTypeResolver\Source\ClassStringInterface;
use TomasVotruba\ClassLeak\Tests\ConstructorParamTypeResolver\Source\GenericInjectedInterface;
use TomasVotruba\ClassLeak\Tests\ConstructorParamTypeResolver\Source\ListValueInjectedInterface;
use TomasVotruba\ClassLeak\Tests\ConstructorParamTypeResolver\Source\NotInjectedInterface;
use TomasVotruba\ClassLeak\Tests\ConstructorParamTypeResolver\Source\UnknownParamInterface;

final class WithDocBlockCollectionInjection
{
    /**
     * @param ArrayInjectedInterface[] $arrayItems
     * @param array<string, GenericInjectedInterface> $genericItems
     * @param list<ListValueInjectedInterface|null> $listItems
     * @param AliasedAlias[] $aliasedItems
     * @param non-empty-array<\TomasVotruba\ClassLeak\Tests\ConstructorParamTypeResolver\Source\FullyQualifiedInjectedInterface> $fullyQualifiedItems
     * @param iterable<Source\IterableInjectedInterface> $iterableItems
     * @param string[] $names
     * @param class-string<ClassStringInterface> $className
     * @param UnknownParamInterface[] $missing
     */
    public function __construct(
        array $arrayItems,
        array $genericItems,
        array $listItems,
        array $aliasedItems,
        array $fullyQualifiedItems,
        iterable $iterableItems,
        array $names,
        string $className
    ) {
    }

    /**
     * @param NotInjectedInterface[] $notInjected
     */
    public function doStuff(array $notInjected): void
    {
    }
}
