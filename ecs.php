<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/bin',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withSkip([
        // invalid syntax test fixture
        __DIR__ . '/tests/UseImportsResolver/Fixture/ParseError.php',
        // keeps @param of a missing constructor param on purpose
        __DIR__ . '/tests/ConstructorParamTypeResolver/Fixture/WithDocBlockCollectionInjection.php',
    ])
    ->withPreparedSets(psr12: true, common: true);
