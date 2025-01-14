<?php

namespace ClassLeak202501\Illuminate\Contracts\Container;

use Exception;
use ClassLeak202501\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
