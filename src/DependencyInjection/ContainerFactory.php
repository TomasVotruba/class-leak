<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\DependencyInjection;

use Entropy\Console\Output\HelpPrinter;
use Entropy\Container\Container;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use TomasVotruba\ClassLeak\Commands\ListCommand;

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

        // register manually, as the lazy HelpPrinter dependency cannot be autowired
        $container->service(
            ListCommand::class,
            static fn (Container $container): ListCommand => new ListCommand(
                static fn (): HelpPrinter => $container->make(HelpPrinter::class)
            )
        );

        $container->autodiscover(__DIR__ . '/..');

        $container->service(Parser::class, static function (): Parser {
            $parserFactory = new ParserFactory();
            return $parserFactory->createForHostVersion();
        });

        return $container;
    }
}
