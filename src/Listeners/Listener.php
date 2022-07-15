<?php

namespace RotHub\Laravel\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;

abstract class Listener implements ShouldQueue
{
    /**
     * @var int 任务被处理的延迟时间（秒）.
     */
    public $delay = 0;
    /**
     * @var int 尝试队列侦听器的次数.
     */
    public $tries = 5;

    /**
     * 确定监听器是否应加入队列.
     *
     * @param mixed $event
     * @return bool
     */
    public function shouldQueue($event)
    {
        return true;
    }
}
