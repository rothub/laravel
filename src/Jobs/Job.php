<?php

namespace RotHub\Laravel\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int 任务可尝试次数.
     */
    public $tries = 5;

    /**
     * 计算重试任务前等待的秒数.
     *
     * @return array
     */
    public function backoff()
    {
        return [1, 60, 600, 1800, 3600 * 24];
    }

    /**
     * 确定任务应该超时的时间.
     *
     * @return \DateTime
     */
    public function retryUntil()
    {
        return now()->addMinutes(10);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    abstract public function handle();

    /**
     * 处理任务失败.
     *
     * @param \Throwable $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        // 向用户发送失败通知等......
    }
}
