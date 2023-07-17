<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Tests;

use PHPUnit\Framework\TestCase;
use TomasVotruba\ClassLeak\DependencyInjection\ContainerFactory;
use Webmozart\Assert\Assert;

abstract class AbstractTestCase extends TestCase
{
    /**
     * @template TType as object
     * @param class-string<TType> $type
     * @return TType
     */
    protected function make(string $type): object
    {
        $containerFactory = new ContainerFactory();
        $container = $containerFactory->create();

        $service = $container->make($type);
        Assert::isInstanceOf($service, $type);

        return $service;
    }
}
