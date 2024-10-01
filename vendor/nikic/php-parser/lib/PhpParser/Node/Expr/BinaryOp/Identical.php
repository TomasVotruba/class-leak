<?php

declare (strict_types=1);
namespace ClassLeak202410\PhpParser\Node\Expr\BinaryOp;

use ClassLeak202410\PhpParser\Node\Expr\BinaryOp;
class Identical extends BinaryOp
{
    public function getOperatorSigil() : string
    {
        return '===';
    }
    public function getType() : string
    {
        return 'Expr_BinaryOp_Identical';
    }
}
