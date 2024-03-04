<?php

declare (strict_types=1);
namespace ClassLeak202403\PhpParser\Node\Expr\BinaryOp;

use ClassLeak202403\PhpParser\Node\Expr\BinaryOp;
class BooleanOr extends BinaryOp
{
    public function getOperatorSigil() : string
    {
        return '||';
    }
    public function getType() : string
    {
        return 'Expr_BinaryOp_BooleanOr';
    }
}
