<?php

namespace ClassLeak202403\Illuminate\Contracts\Container;

use Exception;
use ClassLeak202403\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
