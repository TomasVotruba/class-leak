<?php

namespace ClassLeak202410\Illuminate\Container;

use Exception;
use ClassLeak202410\Psr\Container\NotFoundExceptionInterface;
class EntryNotFoundException extends Exception implements NotFoundExceptionInterface
{
    //
}
