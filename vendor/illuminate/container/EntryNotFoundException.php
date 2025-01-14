<?php

namespace ClassLeak202501\Illuminate\Container;

use Exception;
use ClassLeak202501\Psr\Container\NotFoundExceptionInterface;
class EntryNotFoundException extends Exception implements NotFoundExceptionInterface
{
    //
}
