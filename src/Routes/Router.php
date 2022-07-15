<?php

namespace RotHub\Laravel\Routes;

use Illuminate\Routing\Router as LaravelRouter;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class Router
{
    /**
     * 注册路由.
     * 
     * @param string|array $methods 请求方式.
     * @return void
     */
    public static function make(string|array $methods = ['GET', 'POST']): void
    {
        $uri = Request::path();

        $items = array_map('Str::studly', explode('/', $uri));
        $action = lcfirst(array_pop($items));
        $action = join('\\', $items) . 'Controller@' . $action;

        Route::match($methods, $uri, $action);
    }

    /**
     * 批量注册路由.
     * 
     * @param LaravelRouter $router 路由.
     * @param string $path 路径.
     * @param string|array $methods 请求方式.
     * @return void
     */
    public static function batch(
        LaravelRouter $router,
        string $path = '',
        string|array $methods = ['GET', 'POST']
    ): void {
        $instance = new static;
        $namespace = $instance->namespace($router);
        $controllers = $instance->controllers($path);

        foreach ($controllers as $controller) {
            $items = explode(DIRECTORY_SEPARATOR, $controller);
            $class = join('\\', $items);
            $actions = $instance->methods($namespace . '\\' . $class);

            foreach ($actions as $action) {
                $uris = $instance->uri($items, $action);

                foreach ($uris as $uri) {
                    Route::match($methods, $uri, $class . '@' . $action);
                }
            }
        }
    }

    protected function namespace(LaravelRouter $router): string
    {
        $stack = $router->getGroupStack();
        return end($stack)['namespace'];
    }

    protected function controllers(string $path): array
    {
        $path or $path = app_path('Http/Controllers');
        $path = realpath($path);

        $files = $this->files($path, '*?Controller.php');

        array_walk($files, function (&$v, $k, $search) {
            $v = substr(str_replace($search, '', $v), 1, -4);
        }, $path);

        return $files;
    }

    protected function methods(string $class): array
    {
        $actions = [];
        $filter = $this->filterActions();

        $ref = new \ReflectionClass($class);
        if (!$ref->isAbstract()) {
            $methods = $ref->getMethods(\ReflectionMethod::IS_PUBLIC);

            foreach ($methods as $method) {
                $action = $method->getName();

                if (
                    !in_array($action, $filter)
                    && substr($action, 0, 2) !== '__'
                ) {
                    $actions[] = $action;
                }
            }
        }

        return $actions;
    }

    protected function uri(array $items, string $action): array
    {
        $uri = substr(join('/', array_map('lcfirst', $items)), 0, -10);

        return [
            $uri . '/' . $action,
            Str::snake($uri, '-') . '/' . Str::snake($action, '-'),
            Str::snake($uri, '_') . '/' . Str::snake($action, '_'),
        ];
    }

    protected function files(string $path, string $pattern = '*'): array
    {
        $path .= DIRECTORY_SEPARATOR;
        $dirs = glob($path . '*', GLOB_ONLYDIR);
        $files = glob($path . $pattern);
        $files = $files ? $files : [];

        $res =  array_filter($files, 'is_file');

        foreach ($dirs as $dir) {
            if (is_dir($dir)) {
                $sons = $this->files($dir, $pattern);

                $res = array_merge($res, $sons);
            }
        }

        return $res;
    }

    protected function filterActions(): array
    {
        $actions = [];

        $ref = new \ReflectionClass(\RotHub\Laravel\Http\Controllers\Controller::class);
        $methods = $ref->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            $actions[] = $method->getName();
        }

        return $actions;
    }
}
