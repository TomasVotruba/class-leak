<?php

declare (strict_types=1);
namespace ClassLeak202404\PhpParser\Node\Scalar\MagicConst;

use ClassLeak202404\PhpParser\Node\Scalar\MagicConst;
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
