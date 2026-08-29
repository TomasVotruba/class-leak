<?php

declare(strict_types=1);

namespace JMS\Serializer\Handler;

if (interface_exists(SubscribingHandlerInterface::class)) {
    return;
}

interface SubscribingHandlerInterface
{
    public static function getSubscribingMethods();
}
