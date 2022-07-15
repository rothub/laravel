<?php

namespace RotHub\Laravel\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ModelScope implements Scope
{
    /**
     * @inheritdoc
     */
    public function apply(Builder $builder, Model $model)
    {
        $column = $model->qualifyColumn($model->domainAt());

        return $builder->where([[$column, $model->domain()]]);
    }
}
