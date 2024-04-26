<?php

namespace ClassLeak202404\Illuminate\Container;

use Exception;
use ClassLeak202404\Psr\Container\NotFoundExceptionInterface;
class EntryNotFoundException extends Exception implements NotFoundExceptionInterface
{
    //
}
