<?php

declare (strict_types=1);
namespace ClassLeak202402\PhpParser\Node\Expr\AssignOp;

use ClassLeak202402\PhpParser\Node\Expr\AssignOp;
class BitwiseAnd extends AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_BitwiseAnd';
    }
}
