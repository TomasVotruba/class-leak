<?php

namespace ClassLeak202406\Illuminate\Container;

use Exception;
use ClassLeak202406\Psr\Container\NotFoundExceptionInterface;
class EntryNotFoundException extends Exception implements NotFoundExceptionInterface
{
    //
}
