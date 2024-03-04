<?php

declare (strict_types=1);
namespace ClassLeak202403\PhpParser\Node\Expr\AssignOp;

use ClassLeak202403\PhpParser\Node\Expr\AssignOp;
class Div extends AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_Div';
    }
}
