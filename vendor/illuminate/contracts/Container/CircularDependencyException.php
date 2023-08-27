<?php

namespace ClassLeak202308\Illuminate\Contracts\Container;

use Exception;
use ClassLeak202308\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
