<?php

declare (strict_types=1);
namespace ClassLeak202407\PhpParser\Node\Expr\AssignOp;

use ClassLeak202407\PhpParser\Node\Expr\AssignOp;
class BitwiseOr extends AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_BitwiseOr';
    }
}
