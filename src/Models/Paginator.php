<?php

namespace RotHub\Laravel\Models;

use Illuminate\Pagination\LengthAwarePaginator;

class Paginator extends LengthAwarePaginator
{
    /**
     * @inheritdoc
     */
    public function toArray()
    {
        return [
            config('rothub.PAGE_NO_AT') => $this->currentPage(),
            config('rothub.PAGE_SIZE_AT') => $this->perPage(),
            'total' => $this->total(),
            'list' => $this->items->toArray(),
        ];
    }
}
