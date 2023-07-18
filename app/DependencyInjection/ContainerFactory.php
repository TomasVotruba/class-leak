<?php

declare (strict_types=1);
namespace TomasVotruba\ClassLeak\DependencyInjection;

use ClassLeak202307\Illuminate\Container\Container;
use ClassLeak202307\PhpParser\Parser;
use ClassLeak202307\PhpParser\ParserFactory;
use ClassLeak202307\Symfony\Component\Console\Input\ArrayInput;
use ClassLeak202307\Symfony\Component\Console\Output\ConsoleOutput;
use ClassLeak202307\Symfony\Component\Console\Output\NullOutput;
use ClassLeak202307\Symfony\Component\Console\Style\SymfonyStyle;
final class ContainerFactory
{
    public function create() : Container
    {
        $container = new Container();
        $container->singleton(Parser::class, static function () : Parser {
            $parserFactory = new ParserFactory();
            return $parserFactory->create(ParserFactory::PREFER_PHP7);
        });
        $container->singleton(SymfonyStyle::class, static function () : SymfonyStyle {
            // use null output ofr tests to avoid printing
            $consoleOutput = \defined('ClassLeak202307\\PHPUNIT_COMPOSER_INSTALL') ? new NullOutput() : new ConsoleOutput();
            return new SymfonyStyle(new ArrayInput([]), $consoleOutput);
        });
        return $container;
    }
}
