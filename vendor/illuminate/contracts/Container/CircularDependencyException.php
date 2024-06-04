<?php

namespace ClassLeak202406\Illuminate\Contracts\Container;

use Exception;
use ClassLeak202406\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
