<?php

declare (strict_types=1);
namespace ClassLeak202501\PhpParser\Node\Scalar;

use ClassLeak202501\PhpParser\Node\InterpolatedStringPart;
require __DIR__ . '/../InterpolatedStringPart.php';
if (\false) {
    // For classmap-authoritative support.
    class EncapsedStringPart extends InterpolatedStringPart
    {
    }
}
