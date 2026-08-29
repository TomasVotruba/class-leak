<?php

declare(strict_types=1);

namespace Symfony\Component\Form;

use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractType
{
    public function configureOptions(OptionsResolver $optionsResolver): void
    {
    }
}
