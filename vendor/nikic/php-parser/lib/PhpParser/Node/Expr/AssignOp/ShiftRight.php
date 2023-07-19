<?php

declare (strict_types=1);
namespace ClassLeak202307\PhpParser\Node\Expr\AssignOp;

use ClassLeak202307\PhpParser\Node\Expr\AssignOp;
class ShiftRight extends AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_ShiftRight';
    }
}
