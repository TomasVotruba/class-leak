<?php

namespace ClassLeak202412\Illuminate\Contracts\Container;

use Exception;
use ClassLeak202412\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
