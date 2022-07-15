<?php

namespace RotHub\Laravel\Models;

use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Builder as LaravelBuilder;
use RotHub\Laravel\Models\Paginator;

class Builder extends LaravelBuilder
{
    /**
     * @inheritdoc
     */
    protected function paginator($items, $total, $perPage, $currentPage, $options)
    {
        return Container::getInstance()
            ->makeWith(Paginator::class, compact(
                'items',
                'total',
                'perPage',
                'currentPage',
                'options'
            ));
    }

    /**
     * @inheritdoc
     */
    public function dd()
    {
        $format = str_replace('?', '%s', $this->toSql());
        $values = collect($this->getBindings())
            ->map(function ($binding) {
                return is_numeric($binding) ? $binding : "'{$binding}'";
            })->toArray();

        return vsprintf($format, $values);
    }
}
