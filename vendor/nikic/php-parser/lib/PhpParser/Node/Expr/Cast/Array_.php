<?php

declare (strict_types=1);
namespace ClassLeak202402\PhpParser\Node\Expr\Cast;

use ClassLeak202402\PhpParser\Node\Expr\Cast;
class Array_ extends Cast
{
    public function getType() : string
    {
        return 'Expr_Cast_Array';
    }
}
