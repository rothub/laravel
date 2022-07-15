<?php

namespace RotHub\Laravel\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as LaravelAuthServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends LaravelAuthServiceProvider
{
    /**
     * @inheritdoc
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::guessPolicyNamesUsing(function ($class) {
            return '\\App\\Policies\\' . class_basename($class) . 'Policy';
        });
    }
}
