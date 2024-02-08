<?php

namespace ClassLeak202402\Illuminate\Contracts\Container;

use Exception;
use ClassLeak202402\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
