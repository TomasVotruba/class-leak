<?php

declare (strict_types=1);
namespace TomasVotruba\ClassLeak\DependencyInjection;

use ClassLeak202308\Illuminate\Container\Container;
use ClassLeak202308\PhpParser\Parser;
use ClassLeak202308\PhpParser\ParserFactory;
use ClassLeak202308\Symfony\Component\Console\Application;
use ClassLeak202308\Symfony\Component\Console\Input\ArrayInput;
use ClassLeak202308\Symfony\Component\Console\Output\ConsoleOutput;
use ClassLeak202308\Symfony\Component\Console\Output\NullOutput;
use ClassLeak202308\Symfony\Component\Console\Style\SymfonyStyle;
use TomasVotruba\ClassLeak\Console\Commands\CheckCommand;
use TomasVotruba\ClassLeak\Helpers\PrivatesAccessor;
final class ContainerFactory
{
    /**
     * @api
     */
    public function create() : Container
    {
        $container = new Container();
        $container->singleton(Parser::class, static function () : Parser {
            $parserFactory = new ParserFactory();
            return $parserFactory->create(ParserFactory::PREFER_PHP7);
        });
        $container->singleton(SymfonyStyle::class, static function () : SymfonyStyle {
            // use null output ofr tests to avoid printing
            $consoleOutput = \defined('PHPUNIT_COMPOSER_INSTALL') ? new NullOutput() : new ConsoleOutput();
            return new SymfonyStyle(new ArrayInput([]), $consoleOutput);
        });
        $container->singleton(Application::class, function (Container $container) : Application {
            /** @var CheckCommand $checkCommand */
            $checkCommand = $container->make(CheckCommand::class);
            $application = new Application();
            $application->add($checkCommand);
            $this->cleanupDefaultCommands($application);
            return $application;
        });
        return $container;
    }
    private function cleanupDefaultCommands(Application $application) : void
    {
        PrivatesAccessor::propertyClosure($application, 'commands', static function (array $commands) : array {
            // remove default commands, as not needed here
            unset($commands['completion']);
            unset($commands['help']);
            return $commands;
        });
    }
}
