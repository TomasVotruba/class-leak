<?php

declare (strict_types=1);
namespace ClassLeak202412\PhpParser\Node\Expr\BinaryOp;

use ClassLeak202412\PhpParser\Node\Expr\BinaryOp;
class ShiftRight extends BinaryOp
{
    public function getOperatorSigil() : string
    {
        return '>>';
    }
    public function getType() : string
    {
        return 'Expr_BinaryOp_ShiftRight';
    }
}
