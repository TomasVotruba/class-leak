<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withPreparedSets(
        deadCode: true, naming: true, privatization: true, earlyReturn: true, codeQuality: true, codingStyle: true, typeDeclarations: true, phpunitCodeQuality: true
    )
    ->withPhpSets()
    ->withRootFiles()
    ->withImportNames()
    ->withSkip(['*/scoper.php', '*/Source/*', '*/Fixture/*']);
