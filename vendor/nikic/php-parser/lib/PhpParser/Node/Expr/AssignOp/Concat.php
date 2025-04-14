<?php

declare (strict_types=1);
namespace ClassLeak202504\PhpParser\Node\Expr\AssignOp;

use ClassLeak202504\PhpParser\Node\Expr\AssignOp;
class Concat extends AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_Concat';
    }
}
