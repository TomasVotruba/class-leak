<?php

declare (strict_types=1);
namespace ClassLeak202402\PhpParser\Node\Scalar\MagicConst;

use ClassLeak202402\PhpParser\Node\Scalar\MagicConst;
class Function_ extends MagicConst
{
    public function getName() : string
    {
        return '__FUNCTION__';
    }
    public function getType() : string
    {
        return 'Scalar_MagicConst_Function';
    }
}
