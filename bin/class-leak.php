<?php

declare(strict_types=1);

use Illuminate\Contracts\Console\Kernel;

require_once __DIR__ . '/../vendor/autoload.php';

/** @var \Illuminate\Foundation\Application $application */
$application = require_once __DIR__ . '/../bootstrap/app.php';

/** @var Kernel $kernel */
$kernel = $application->make(Kernel::class);

$status = $kernel->handle(
    $input = new Symfony\Component\Console\Input\ArgvInput(),
    new Symfony\Component\Console\Output\ConsoleOutput()
);

$kernel->terminate($input, $status);
exit($status);
