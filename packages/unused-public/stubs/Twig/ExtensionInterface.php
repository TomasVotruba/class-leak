<?php

declare(strict_types=1);

namespace Twig\Extension;

if (interface_exists(ExtensionInterface::class)) {
    return;
}

interface ExtensionInterface
{
    public function getLoader();
}
