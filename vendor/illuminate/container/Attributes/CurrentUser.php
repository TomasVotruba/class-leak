<?php

namespace ClassLeak202412\Illuminate\Container\Attributes;

use Attribute;
#[Attribute(Attribute::TARGET_PARAMETER)]
class CurrentUser extends Authenticated
{
    //
}