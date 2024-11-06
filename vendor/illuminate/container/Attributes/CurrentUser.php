<?php

namespace ClassLeak202411\Illuminate\Container\Attributes;

use Attribute;
#[Attribute(Attribute::TARGET_PARAMETER)]
class CurrentUser extends Authenticated
{
    //
}
