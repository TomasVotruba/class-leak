<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\DependencyInjection;

use Entropy\Container\Container;
use PhpParser\Parser;
use PhpParser\ParserFactory;

/**
 * @api
 */
final class ContainerFactory
{
    /**
     * @api
     */
    public function create(): Container
    {
        $container = new Container();

        $container->autodiscover(__DIR__ . '/..');

        // parse using the newest supported grammar, so the tool detects modern
        // syntax even when it runs on an older PHP version (down to 7.4)
        $container->service(Parser::class, static function (): Parser {
            $parserFactory = new ParserFactory();
            return $parserFactory->createForNewestSupportedVersion();
        });

        return $container;
    }
}
