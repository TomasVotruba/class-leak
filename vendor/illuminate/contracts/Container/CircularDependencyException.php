<?php

namespace ClassLeak202411\Illuminate\Contracts\Container;

use Exception;
use ClassLeak202411\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
