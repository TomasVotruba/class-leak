<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Console;

use Illuminate\Foundation\Console\Kernel;

final class ClassLeakKernel extends Kernel
{
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
    }
}
