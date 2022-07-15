<?php

namespace RotHub\Laravel\Providers;

use Illuminate\Support\ServiceProvider;

class RotHubProvider extends ServiceProvider
{
    /**
     * @inheritdoc
     */
    public function boot()
    {
        $this->config();
    }

    protected function config()
    {
        $path = realpath(__DIR__ . '/../Config/rothub.php');

        $this->publishes([$path => config_path('rothub.php')], 'config');
        $this->mergeConfigFrom($path, 'rothub');
    }
}
