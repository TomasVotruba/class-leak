<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Tests\PHPStan;

use ReflectionMethod;
use ShipMonk\PHPStan\DeadCode\Provider\ReflectionBasedMemberUsageProvider;
use ShipMonk\PHPStan\DeadCode\Provider\VirtualUsageData;

// services are instantiated via the entropy DI container through reflection
final class DiConstructorUsageProvider extends ReflectionBasedMemberUsageProvider
{
    protected function shouldMarkMethodAsUsed(ReflectionMethod $method): ?VirtualUsageData
    {
        if ($method->isConstructor()) {
            return VirtualUsageData::withNote('Instantiated via entropy DI container');
        }

        return null;
    }
}
