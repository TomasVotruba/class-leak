<?php

namespace ClassLeak202504\Illuminate\Container;

use Exception;
use ClassLeak202504\Psr\Container\NotFoundExceptionInterface;
class EntryNotFoundException extends Exception implements NotFoundExceptionInterface
{
    //
}
