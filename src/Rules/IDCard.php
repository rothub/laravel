<?php

namespace RotHub\Laravel\Rules;

use Illuminate\Contracts\Validation\Rule;

class IDCard implements Rule
{
    /**
     * @inheritdoc
     */
    public function passes($attribute, $value)
    {
        if (strlen($value) != 18) {
            return false;
        }

        // 本体码.
        $code = substr($value, 0, 17);
        // 加权因子.
        $factor = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
        // 校验码对应值.
        $check = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'];
        // 根据前17位计算校验码.
        $total = 0;
        for ($i = 0; $i < 17; $i++) {
            $total += substr($code, $i, 1) * $factor[$i];
        }
        // 取模.
        $mod = $total % 11;
        // 比较校验码.
        return substr($value, 17, 1) == $check[$mod];
    }

    /**
     * @inheritdoc
     */
    public function message()
    {
        return 'The :attribute format is invalid.';
    }
}
