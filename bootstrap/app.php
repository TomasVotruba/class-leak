<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Exceptions\Handler;
use TomasVotruba\ClassLeak\Console\ClassLeakKernel;

$application = new Application($_ENV['APP_BASE_PATH'] ?? dirname(__DIR__));

$application->singleton(\Illuminate\Contracts\Console\Kernel::class, ClassLeakKernel::class);
$application->singleton(ExceptionHandler::class, Handler::class);

return $application;
