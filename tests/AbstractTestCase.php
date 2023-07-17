<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase;
use Webmozart\Assert\Assert;

abstract class AbstractTestCase extends TestCase
{
    /**
     * This is magically invoked by parent setUp() call
     * @see \Illuminate\Foundation\Testing\TestCase::refreshApplication
     */
    public function createApplication(): Application
    {
        /** @var Application $application */
        $application = require __DIR__ . '/../bootstrap/app.php';

        /** @var Kernel $kernel */
        $kernel = $application->make(Kernel::class);
        $kernel->bootstrap();

        return $application;
    }

    /**
     * @template TType as object
     * @param class-string<TType> $type
     * @return TType
     */
    protected function make(string $type): object
    {
        $service = $this->app->make($type);
        Assert::isInstanceOf($service, $type);

        return $service;
    }
}
