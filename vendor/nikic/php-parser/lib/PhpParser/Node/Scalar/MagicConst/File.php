<?php

declare (strict_types=1);
namespace ClassLeak202308\PhpParser\Node\Scalar\MagicConst;

use ClassLeak202308\PhpParser\Node\Scalar\MagicConst;
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
