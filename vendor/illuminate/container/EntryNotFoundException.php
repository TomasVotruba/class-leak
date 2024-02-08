<?php

namespace ClassLeak202402\Illuminate\Container;

use Exception;
use ClassLeak202402\Psr\Container\NotFoundExceptionInterface;
class EntryNotFoundException extends Exception implements NotFoundExceptionInterface
{
    //
}
