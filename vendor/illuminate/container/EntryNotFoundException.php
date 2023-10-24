<?php

namespace ClassLeak202310\Illuminate\Container;

use Exception;
use ClassLeak202310\Psr\Container\NotFoundExceptionInterface;
class EntryNotFoundException extends Exception implements NotFoundExceptionInterface
{
    //
}
