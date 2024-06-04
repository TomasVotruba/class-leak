<?php

declare (strict_types=1);
namespace ClassLeak202406\PhpParser\Node\Scalar\MagicConst;

use ClassLeak202406\PhpParser\Node\Scalar\MagicConst;
class File extends MagicConst
{
    public function getName() : string
    {
        return '__FILE__';
    }
    public function getType() : string
    {
        return 'Scalar_MagicConst_File';
    }
}
