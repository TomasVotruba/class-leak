<?php

namespace ClassLeak202404\Illuminate\Contracts\Container;

use Exception;
use ClassLeak202404\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
