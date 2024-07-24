<?php

namespace ClassLeak202407\Illuminate\Contracts\Container;

use Exception;
use ClassLeak202407\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
