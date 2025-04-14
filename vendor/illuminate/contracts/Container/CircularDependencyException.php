<?php

namespace ClassLeak202504\Illuminate\Contracts\Container;

use Exception;
use ClassLeak202504\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
