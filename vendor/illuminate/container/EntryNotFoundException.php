<?php

namespace ClassLeak202407\Illuminate\Container;

use Exception;
use ClassLeak202407\Psr\Container\NotFoundExceptionInterface;
class EntryNotFoundException extends Exception implements NotFoundExceptionInterface
{
    //
}
