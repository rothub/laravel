<?php

namespace RotHub\Laravel\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification as LaravelNotification;

class Notification extends LaravelNotification implements ShouldQueue
{
    use Queueable;
}
