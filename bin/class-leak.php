<?php

declare (strict_types=1);
namespace ClassLeak202412;

use ClassLeak202412\Symfony\Component\Console\Application;
use ClassLeak202412\Symfony\Component\Console\Input\ArgvInput;
use ClassLeak202412\Symfony\Component\Console\Output\ConsoleOutput;
use TomasVotruba\ClassLeak\DependencyInjection\ContainerFactory;
if (\file_exists(__DIR__ . '/../../../../vendor/autoload.php')) {
    // project's autoload
    require_once __DIR__ . '/../../../../vendor/autoload.php';
}
if (\file_exists(__DIR__ . '/../vendor/scoper-autoload.php')) {
    // A. build downgraded package
    require_once __DIR__ . '/../vendor/scoper-autoload.php';
} else {
    // B. local repository
    require_once __DIR__ . '/../vendor/autoload.php';
}
$containerFactory = new ContainerFactory();
$container = $containerFactory->create();
/** @var Application $application */
$application = $container->make(Application::class);
$exitCode = $application->run(new ArgvInput(), new ConsoleOutput());
exit($exitCode);
