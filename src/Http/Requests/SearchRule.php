<?php

namespace RotHub\Laravel\Http\Requests;

use RotHub\Laravel\Http\Requests\Request;
use RotHub\Laravel\Rules\Rule;

class SearchRule extends Request
{
    /**
     * @inheritdoc
     */
    public static function defaultRules(): array
    {
        return Rule::page();
    }
}
