<?php

namespace RotHub\Laravel\Providers;

use Illuminate\Database\Events\StatementPrepared;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * @inheritdoc
     */
    public function boot()
    {
        Event::listen(StatementPrepared::class, function ($event) {
            $event->statement->setFetchMode(\PDO::FETCH_ASSOC);
        });
    }

    /**
     * 确定是否应用自动发现事件和侦听器.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return true;
    }
}
