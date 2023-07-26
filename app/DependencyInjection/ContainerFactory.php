<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\DependencyInjection;

use Illuminate\Container\Container;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ContainerFactory
{
    /**
     * @api
     */
    public function create(): Container
    {
        $container = new Container();

        $container->singleton(Parser::class, static function (): Parser {
            $parserFactory = new ParserFactory();
            return $parserFactory->create(ParserFactory::PREFER_PHP7);
        });

        $container->singleton(
            SymfonyStyle::class,
            static function (): SymfonyStyle {
                // use null output ofr tests to avoid printing
                $consoleOutput = defined('PHPUNIT_COMPOSER_INSTALL') ? new NullOutput() : new ConsoleOutput();
                return new SymfonyStyle(new ArrayInput([]), $consoleOutput);
            }
        );

        return $container;
    }
}
