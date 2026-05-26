<?php

namespace ClassLeak202605\Illuminate\Container;

use Exception;
use ClassLeak202605\Psr\Container\NotFoundExceptionInterface;
class EntryNotFoundException extends Exception implements NotFoundExceptionInterface
{
    //
}
