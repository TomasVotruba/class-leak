<?php

declare (strict_types=1);
namespace ClassLeak202311\PhpParser\Node\Expr\Cast;

use ClassLeak202311\PhpParser\Node\Expr\Cast;
class Object_ extends Cast
{
    public function getType() : string
    {
        return 'Expr_Cast_Object';
    }
}
