<?php

declare (strict_types=1);
namespace ClassLeak202410\PhpParser\Node\Expr\Cast;

use ClassLeak202410\PhpParser\Node\Expr\Cast;
class Int_ extends Cast
{
    public function getType() : string
    {
        return 'Expr_Cast_Int';
    }
}
