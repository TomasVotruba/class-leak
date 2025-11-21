<?php

namespace ClassLeak202511\Illuminate\Container;

use Exception;
use ClassLeak202511\Psr\Container\NotFoundExceptionInterface;
class EntryNotFoundException extends Exception implements NotFoundExceptionInterface
{
    //
}
