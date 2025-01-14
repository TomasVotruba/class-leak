<?php

declare (strict_types=1);
namespace ClassLeak202501\PhpParser\Node\Expr\BinaryOp;

use ClassLeak202501\PhpParser\Node\Expr\BinaryOp;
class Pow extends BinaryOp
{
    public function getOperatorSigil() : string
    {
        return '**';
    }
    public function getType() : string
    {
        return 'Expr_BinaryOp_Pow';
    }
}
