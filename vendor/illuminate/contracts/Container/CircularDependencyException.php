<?php

namespace ClassLeak202310\Illuminate\Contracts\Container;

use Exception;
use ClassLeak202310\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
