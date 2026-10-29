<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Tests\EndToEnd\Fixture\MethodLevelListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

final class MethodLevelSubscriber
{
    #[AsEventListener(event: 'some.event')]
    public function onEvent(): void
    {
    }
}
