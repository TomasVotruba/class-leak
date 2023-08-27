<?php

namespace ClassLeak202308\Illuminate\Container;

use Exception;
use ClassLeak202308\Psr\Container\NotFoundExceptionInterface;
class EntryNotFoundException extends Exception implements NotFoundExceptionInterface
{
    //
}
