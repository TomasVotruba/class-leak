<?php

declare (strict_types=1);
namespace ClassLeak202308\PhpParser\Node\Expr\AssignOp;

use ClassLeak202308\PhpParser\Node\Expr\AssignOp;
class Pow extends AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_Pow';
    }
}
