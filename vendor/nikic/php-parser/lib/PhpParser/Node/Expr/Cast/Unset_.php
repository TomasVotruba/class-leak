<?php

declare (strict_types=1);
namespace ClassLeak202411\PhpParser\Node\Expr\Cast;

use ClassLeak202411\PhpParser\Node\Expr\Cast;
class Unset_ extends Cast
{
    public function getType() : string
    {
        return 'Expr_Cast_Unset';
    }
}