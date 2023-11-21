<?php

namespace ClassLeak202311\Illuminate\Container;

use Exception;
use ClassLeak202311\Psr\Container\NotFoundExceptionInterface;
class EntryNotFoundException extends Exception implements NotFoundExceptionInterface
{
    //
}
