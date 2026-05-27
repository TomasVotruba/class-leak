<?php

namespace ClassLeak202605\Illuminate\Contracts\Container;

use Exception;
use ClassLeak202605\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
