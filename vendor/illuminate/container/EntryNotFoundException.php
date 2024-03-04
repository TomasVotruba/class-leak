<?php

namespace ClassLeak202403\Illuminate\Container;

use Exception;
use ClassLeak202403\Psr\Container\NotFoundExceptionInterface;
class EntryNotFoundException extends Exception implements NotFoundExceptionInterface
{
    //
}
