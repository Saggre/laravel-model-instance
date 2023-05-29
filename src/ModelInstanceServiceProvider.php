<?php

namespace Saggre\LaravelModelInstance;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Tinker\Console\TinkerCommand;

class ModelInstanceServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $source = realpath($raw = __DIR__ . '/../config/tinker.php') ?: $raw;

        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('tinker.php')]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('tinker');
        }

        $this->mergeConfigFrom($source, 'tinker');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('command.tinker', function () {
            return new TinkerCommand;
        });

        $this->commands(['command.tinker']);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['command.tinker'];
    }
}