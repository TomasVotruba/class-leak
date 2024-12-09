<?php

declare (strict_types=1);
namespace ClassLeak202412\PhpParser\Node\Expr\AssignOp;

use ClassLeak202412\PhpParser\Node\Expr\AssignOp;
class Mul extends AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_Mul';
    }
}
