<?php

declare(strict_types=1);

use TomasVotruba\ClassLeak\Config\Contract\ConfigFileAnalyzerInterface;
use TomasVotruba\ClassLeak\Config\EasyCIConfig;
use TomasVotruba\ClassLeak\Twig\TwigTemplateAnalyzer\ConstantPathTwigTemplateAnalyzer;
use TomasVotruba\ClassLeak\Twig\TwigTemplateAnalyzer\MissingClassConstantTwigAnalyzer;

return static function (EasyCIConfig $easyCIConfig): void {
    $easyCIConfig->paths([__DIR__ . '/app', __DIR__ . '/config']);

    $easyCIConfig->typesToSkip([
        ConstantPathTwigTemplateAnalyzer::class,
        MissingClassConstantTwigAnalyzer::class,
        ConfigFileAnalyzerInterface::class,
    ]);
};
