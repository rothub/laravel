<?php

namespace RotHub\Laravel\Rules;

use Illuminate\Contracts\Validation\Rule;

class Phone implements Rule
{
    /**
     * @inheritdoc
     */
    public function passes($attribute, $value)
    {
        return preg_match("/^1[3456789]{1}\d{9}$/", $value);
    }

    /**
     * @inheritdoc
     */
    public function message()
    {
        return 'The :attribute format is invalid.';
    }
}
