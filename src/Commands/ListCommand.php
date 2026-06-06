<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Commands;

use Closure;
use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;
use Entropy\Console\Output\HelpPrinter;

final readonly class ListCommand implements CommandInterface
{
    /**
     * @param Closure():HelpPrinter $helpPrinterFactory Lazy, as HelpPrinter needs CommandRegistry with this command
     */
    public function __construct(
        private Closure $helpPrinterFactory,
    ) {
    }

    public function getName(): string
    {
        return 'list';
    }

    public function getDescription(): string
    {
        return 'List available commands';
    }

    /**
     * @api called by entropy console via reflection
     *
     * @param bool $ansi Kept for backward compatibility, colored output is always on
     */
    public function run(bool $ansi = false): int
    {
        $helpPrinter = ($this->helpPrinterFactory)();
        $helpPrinter->print();

        return ExitCode::SUCCESS;
    }
}
