<?php

declare(strict_types=1);

namespace Symfony\Bundle\FrameworkBundle\Controller;

if (class_exists(Controller::class)) {
    return;
}

abstract class Controller
{
}
