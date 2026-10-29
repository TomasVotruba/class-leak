<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Tests\EndToEnd\Fixture\InheritedListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

abstract class AbstractListenerSubscriber
{
    #[AsEventListener(event: 'some.event')]
    public function onEvent(): void
    {
    }
}
