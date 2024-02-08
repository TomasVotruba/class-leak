<?php

declare (strict_types=1);
namespace TomasVotruba\ClassLeak\DependencyInjection;

use ClassLeak202402\Illuminate\Container\Container;
use ClassLeak202402\PhpParser\Parser;
use ClassLeak202402\PhpParser\ParserFactory;
use ClassLeak202402\Symfony\Component\Console\Application;
use ClassLeak202402\Symfony\Component\Console\Input\ArrayInput;
use ClassLeak202402\Symfony\Component\Console\Output\ConsoleOutput;
use ClassLeak202402\Symfony\Component\Console\Output\NullOutput;
use ClassLeak202402\Symfony\Component\Console\Style\SymfonyStyle;
use TomasVotruba\ClassLeak\Commands\CheckCommand;
/**
 * @api
 */
final class ContainerFactory
{
    /**
     * @var string[]
     */
    private const COMMAND_NAMES_TO_HIDE = ['completion', 'help', 'list'];
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
            $this->hideDefaultCommands($application);
            return $application;
        });
        return $container;
    }
    /**
     * @see https://tomasvotruba.com/blog/how-make-your-tool-commands-list-easy-to-read
     */
    private function hideDefaultCommands(Application $application) : void
    {
        foreach (self::COMMAND_NAMES_TO_HIDE as $commandNameToHide) {
            $commandToHide = $application->get($commandNameToHide);
            $commandToHide->setHidden();
        }
    }
}
