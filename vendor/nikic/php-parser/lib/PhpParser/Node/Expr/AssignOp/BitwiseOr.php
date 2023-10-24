<?php

declare (strict_types=1);
namespace ClassLeak202310\PhpParser\Node\Expr\AssignOp;

use ClassLeak202310\PhpParser\Node\Expr\AssignOp;
class BitwiseOr extends AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_BitwiseOr';
    }
}
