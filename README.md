# laravel

## 1. 发布配置文件.

```
php artisan vendor:publish --provider="RotHub\Laravel\Providers\RotHubProvider"
```

## 2. 注册服务提供者.

config/app.php

```
'providers' => [
    ...
    Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class,
    RotHub\Laravel\Providers\AppServiceProvider::class,
    RotHub\Laravel\Providers\AuthServiceProvider::class,
    RotHub\Laravel\Providers\ExceptionServiceProvider::class,
    RotHub\Laravel\Providers\RouteServiceProvider::class,
]
```

## 3. 注册中间件.

app/Http/Kernel.php

```
protected $middlewareGroups = [
    ...
    'api' => [
        'throttle:api',
        // \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \RotHub\Laravel\Http\Middleware\FormaterResponse::class,
    ],
];

protected $routeMiddleware = [
    ...
    'auth' => \RotHub\Laravel\Http\Middleware\Authenticate::class,
];
```
