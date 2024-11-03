<?php

declare (strict_types=1);
namespace ClassLeak202411\PhpParser\ErrorHandler;

use ClassLeak202411\PhpParser\Error;
use ClassLeak202411\PhpParser\ErrorHandler;
/**
 * Error handler that handles all errors by throwing them.
 *
 * This is the default strategy used by all components.
 */
class Throwing implements ErrorHandler
{
    public function handleError(Error $error)
    {
        throw $error;
    }
}
