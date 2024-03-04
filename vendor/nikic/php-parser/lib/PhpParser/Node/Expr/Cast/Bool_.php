<?php

declare (strict_types=1);
namespace ClassLeak202403\PhpParser\Node\Expr\Cast;

use ClassLeak202403\PhpParser\Node\Expr\Cast;
class Bool_ extends Cast
{
    public function getType() : string
    {
        return 'Expr_Cast_Bool';
    }
}
