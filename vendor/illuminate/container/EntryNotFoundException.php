<?php

namespace ClassLeak202307\Illuminate\Container;

use Exception;
use ClassLeak202307\Psr\Container\NotFoundExceptionInterface;
class EntryNotFoundException extends Exception implements NotFoundExceptionInterface
{
    //
}
