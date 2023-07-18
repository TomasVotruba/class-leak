<?php

declare(strict_types=1);

use TomasVotruba\ClassLeak\Console\ClassLeakApplication;
use TomasVotruba\ClassLeak\DependencyInjection\ContainerFactory;

if (file_exists(__DIR__ . '/../vendor/scoper-autoload.php')) {
    // A. build downgraded package
    require_once  __DIR__ . '/../vendor/scoper-autoload.php';
} elseif (file_exists(__DIR__ . '/../../../../vendor/autoload.php')) {
    // B. dev package
    require_once  __DIR__ . '/../../../../vendor/autoload.php';
} else {
    // C. local repository
    require_once __DIR__ . '/../vendor/autoload.php';
}


$containerFactory = new ContainerFactory();
$container = $containerFactory->create();

/** @var ClassLeakApplication $application */
$application = $container->make(ClassLeakApplication::class);

$input = new Symfony\Component\Console\Input\ArgvInput();
$output = new Symfony\Component\Console\Output\ConsoleOutput();

$exitCode = $application->run($input, $output);
exit($exitCode);
