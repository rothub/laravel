<?php

namespace RotHub\Laravel\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as LaravelController;
use Illuminate\Support\Facades\Request;

abstract class Controller extends LaravelController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @inheritdoc
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * 初始化.
     *
     * @return void
     */
    protected function init(): void
    {
    }

    /**
     * 注册认证中间件.
     *
     * @param string $guard 守卫.
     * @param array $only 仅.
     * @param array $except 除.
     * @return void
     */
    protected function addAuthMiddleware(
        string $guard = '',
        array $only = null,
        array $except = null
    ): void {
        $middleware = $this->middleware('auth:' . $guard);

        is_null($only) or $middleware->only($only);
        is_null($except) or $middleware->except($except);
    }

    /**
     * 自动获取路由守卫.
     *
     * @return string
     */
    protected function guard(): string
    {
        $path = explode('/', Request::path());

        return (string)array_shift($path);
    }
}
