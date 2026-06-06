<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Tests\Commands;

use Entropy\Console\Enum\ExitCode;
use TomasVotruba\ClassLeak\Commands\ListCommand;
use TomasVotruba\ClassLeak\Tests\AbstractTestCase;

final class ListCommandTest extends AbstractTestCase
{
    public function test(): void
    {
        $listCommand = $this->make(ListCommand::class);

        $this->assertSame('list', $listCommand->getName());
        $this->assertSame(ExitCode::SUCCESS, $listCommand->run());
    }
}
