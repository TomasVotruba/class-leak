<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Console;

use Symfony\Component\Console\Application;
use TomasVotruba\ClassLeak\Console\Commands\CheckCommand;

final class ClassLeakApplication extends Application
{
    public function __construct(CheckCommand $checkCommand)
    {
        parent::__construct('Class Leak', '0.1');

        $this->add($checkCommand);
    }
}
