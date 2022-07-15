<?php

namespace RotHub\Laravel\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * @inheritdoc
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        config('app.debug') && DB::listen(function ($query) {
            $sql = str_replace('?', '"' . '%s' . '"', $query->sql);
            $sql = vsprintf($sql, $query->bindings);
            $sql = str_replace("\\", "", $sql);

            Log::debug('DB SQL', [
                'time' => $query->time . 'ms',
                'sql' => $sql,
            ]);
        });
    }
}
