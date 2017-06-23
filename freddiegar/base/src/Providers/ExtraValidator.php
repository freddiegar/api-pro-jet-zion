<?php

namespace FreddieGar\Base\Providers;

use \Illuminate\Validation\Validator;

class ExtraValidator extends Validator
{
    public function validateBothNotFilled($attribute, $value, $parameters)
    {
        return ($value != '' && $this->getValue($parameters[0]) != '') ? false : true;
    }
}
