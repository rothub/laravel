<?php

namespace RotHub\Laravel\Http\Requests;

use Illuminate\Contracts\Validation\Factory as LaravelFactory;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use RotHub\Laravel\Exceptions\Exception;
use Symfony\Component\HttpFoundation\Response as Http;

class Request extends FormRequest
{
    /**
     * @inheritdoc
     */
    protected $stopOnFirstFailure = true;

    /**
     * 默认规则.
     * 
     * @return array
     */
    public static function defaultRules(): array
    {
        return [];
    }

    /**
     * 全部规则.
     * 
     * @return array
     */
    public static function allRules(): array
    {
        $rules1 = static::defaultRules();
        $rules2 = static::rules();

        return array_merge($rules1, $rules2);
    }

    /**
     * @inheritdoc
     */
    public static function rules(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function createDefaultValidator(LaravelFactory $factory)
    {
        $validator = $factory->make(
            $this->validationData(),
            $this->makeRules(),
            $this->messages(),
            $this->attributes()
        );

        if ($validator instanceof \Illuminate\Validation\Validator) {
            $validator->stopOnFirstFailure($this->stopOnFirstFailure);
        }

        return $validator;
    }

    /**
     * @inheritdoc
     */
    protected function failedValidation(Validator $validator)
    {
        $message = $validator->errors()->first();

        Exception::fail($message, Http::HTTP_UNPROCESSABLE_ENTITY, Http::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * 规则.
     * 
     * @return array
     */
    protected function makeRules(): array
    {
        $rules1 = $this->container->call([$this, 'defaultRules']);
        $rules2 = $this->container->call([$this, 'rules']);
        $rules = array_merge($rules1, $rules2);

        foreach ($rules as &$rule) {
            if (is_string($rule)) {
                $rule = 'bail|' . $rule;
            } else if (is_array($rule)) {
                array_unshift($rule, 'bail');
            } else {
                $rule = ['bail', $rule];
            }
        }

        return $rules;
    }
}
