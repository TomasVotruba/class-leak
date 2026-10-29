<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Tests\EndToEnd\Fixture\ClassLevelListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: 'some.event')]
final class ClassLevelSubscriber
{
    public function onEvent(): void
    {
    }
}
