<?php

declare (strict_types=1);
namespace ClassLeak202410\PhpParser\Node\Expr\AssignOp;

use ClassLeak202410\PhpParser\Node\Expr\AssignOp;
class Minus extends AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_Minus';
    }
}
