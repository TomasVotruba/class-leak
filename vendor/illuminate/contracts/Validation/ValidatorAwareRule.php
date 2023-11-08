<?php

namespace ClassLeak202311\Illuminate\Contracts\Validation;

use ClassLeak202311\Illuminate\Validation\Validator;
interface ValidatorAwareRule
{
    /**
     * Set the current validator.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return $this
     */
    public function setValidator(Validator $validator);
}
