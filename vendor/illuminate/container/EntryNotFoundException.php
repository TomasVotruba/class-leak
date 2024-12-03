<?php

namespace ClassLeak202412\Illuminate\Container;

use Exception;
use ClassLeak202412\Psr\Container\NotFoundExceptionInterface;
class EntryNotFoundException extends Exception implements NotFoundExceptionInterface
{
    //
}
