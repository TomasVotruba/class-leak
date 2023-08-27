<?php

declare (strict_types=1);
namespace ClassLeak202308\PhpParser\Node\Expr\Cast;

use ClassLeak202308\PhpParser\Node\Expr\Cast;
class String_ extends Cast
{
    public function getType() : string
    {
        return 'Expr_Cast_String';
    }
}