<?php

namespace RotHub\Laravel\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope as LaravelSoftDeletingScope;

class SoftDeletingScope extends LaravelSoftDeletingScope
{
    /**
     * @inheritdoc
     */
    public function apply(Builder $builder, Model $model)
    {
        $column = $model->qualifyColumn($model->getQualifiedDeletedAtColumn());

        return $builder->where([[$column, '<>', $model->deletedValue]]);
    }

    /**
     * @inheritdoc
     */
    public function extend(Builder $builder)
    {
        foreach ($this->extensions as $extension) {
            $this->{"add{$extension}"}($builder);
        }

        $builder->onDelete(function (Builder $builder) {
            $column = $this->getDeletedAtColumn($builder);

            return $builder->update([
                $column => $builder->getModel()->deletedValue,
            ]);
        });
    }

    /**
     * @inheritdoc
     */
    protected function addRestore(Builder $builder)
    {
        $builder->macro('restore', function (Builder $builder) {
            $builder->withTrashed();

            $model = $builder->getModel();

            return $builder->update([$model->getDeletedAtColumn() => $model->restoredValue]);
        });
    }

    /**
     * @inheritdoc
     */
    protected function addWithoutTrashed(Builder $builder)
    {
        $builder->macro('withoutTrashed', function (Builder $builder) {
            $model = $builder->getModel();

            $builder->withoutGlobalScope($this)
                ->where($model->getQualifiedDeletedAtColumn(), $model->restoredValue);

            return $builder;
        });
    }

    /**
     * @inheritdoc
     */
    protected function addOnlyTrashed(Builder $builder)
    {
        $builder->macro('onlyTrashed', function (Builder $builder) {
            $model = $builder->getModel();

            $builder->withoutGlobalScope($this)
                ->where($model->getQualifiedDeletedAtColumn(), '<>', $model->restoredValue);

            return $builder;
        });
    }
}
