<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Tests\ConstructorParamTypeResolver;

use TomasVotruba\ClassLeak\ConstructorParamTypeResolver;
use TomasVotruba\ClassLeak\Tests\AbstractTestCase;
use TomasVotruba\ClassLeak\Tests\ConstructorParamTypeResolver\Source\AliasedInjectedInterface;
use TomasVotruba\ClassLeak\Tests\ConstructorParamTypeResolver\Source\ArrayInjectedInterface;
use TomasVotruba\ClassLeak\Tests\ConstructorParamTypeResolver\Source\FirstInjectedInterface;
use TomasVotruba\ClassLeak\Tests\ConstructorParamTypeResolver\Source\FullyQualifiedInjectedInterface;
use TomasVotruba\ClassLeak\Tests\ConstructorParamTypeResolver\Source\GenericInjectedInterface;
use TomasVotruba\ClassLeak\Tests\ConstructorParamTypeResolver\Source\IterableInjectedInterface;
use TomasVotruba\ClassLeak\Tests\ConstructorParamTypeResolver\Source\ListValueInjectedInterface;
use TomasVotruba\ClassLeak\Tests\ConstructorParamTypeResolver\Source\SecondInjectedInterface;

final class ConstructorParamTypeResolverTest extends AbstractTestCase
{
    private ConstructorParamTypeResolver $constructorParamTypeResolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->constructorParamTypeResolver = $this->make(ConstructorParamTypeResolver::class);
    }

    public function test(): void
    {
        $resolvedNames = $this->constructorParamTypeResolver->resolve(
            __DIR__ . '/Fixture/WithConstructorInjection.php'
        );

        // only constructor param class types, nullable unwrapped, scalar and method params excluded
        $this->assertSame([FirstInjectedInterface::class, SecondInjectedInterface::class], $resolvedNames);
    }

    public function testDocBlockCollections(): void
    {
        $resolvedNames = $this->constructorParamTypeResolver->resolve(
            __DIR__ . '/Fixture/WithDocBlockCollectionInjection.php'
        );

        // collection element types of constructor @param, scalars, class-string, unknown params and method params excluded
        $this->assertSame([
            ArrayInjectedInterface::class,
            GenericInjectedInterface::class,
            ListValueInjectedInterface::class,
            AliasedInjectedInterface::class,
            FullyQualifiedInjectedInterface::class,
            IterableInjectedInterface::class,
        ], $resolvedNames);
    }
}
