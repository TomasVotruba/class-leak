<?php

declare(strict_types=1);

use Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/bin',
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/packages',
    ])
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        typeDeclarationDocblocks: true,
        privatization: true,
        naming: true,
        earlyReturn: true,
        phpunitCodeQuality: true
    )
    ->withPhpSets()
    ->withRootFiles()
    ->withImportNames()
    ->withSkip([
        '*/scoper.php',
        '*/Source/*',
        '*/Fixture/*',
        StringClassNameToClassConstantRector::class => [
            __DIR__ . '/src/Filtering/PossiblyUnusedClassesFilter.php',
        ]
    ]);
