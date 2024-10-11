<?php

namespace ClassLeak202410\Illuminate\Contracts\Container;

use Exception;
use ClassLeak202410\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
