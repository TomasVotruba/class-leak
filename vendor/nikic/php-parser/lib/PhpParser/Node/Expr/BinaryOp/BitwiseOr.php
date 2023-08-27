<?php

declare (strict_types=1);
namespace ClassLeak202308\PhpParser\Node\Expr\BinaryOp;

use ClassLeak202308\PhpParser\Node\Expr\BinaryOp;
class BitwiseOr extends BinaryOp
{
    public function getOperatorSigil() : string
    {
        return '|';
    }
    public function getType() : string
    {
        return 'Expr_BinaryOp_BitwiseOr';
    }
}