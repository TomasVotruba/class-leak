<?php

namespace ClassLeak202307\Illuminate\Contracts\Container;

use Exception;
use ClassLeak202307\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
