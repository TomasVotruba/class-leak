<?php

namespace ClassLeak202411\Illuminate\Container;

use Exception;
use ClassLeak202411\Psr\Container\NotFoundExceptionInterface;
class EntryNotFoundException extends Exception implements NotFoundExceptionInterface
{
    //
}
