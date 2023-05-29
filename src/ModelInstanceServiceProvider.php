<?php

namespace Saggre\LaravelModelInstance;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Saggre\LaravelModelInstance\Console\ModelInstanceCommand;
use Saggre\LaravelModelInstance\Services\ModelInstanceCommandService;

class ModelInstanceServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $source = realpath($raw = __DIR__.'/../config/instance-command.php') ?: $raw;

        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('instance-command.php')]);
        }

        $this->mergeConfigFrom($source, 'instance-command');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('command.model-instance', function () {
            return new ModelInstanceCommand(
                new ModelInstanceCommandService()
            );
        });

        $this->app->alias('command.model-instance', ModelInstanceCommand::class);

        $this->commands(['command.model-instance']);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['command.model-instance'];
    }
}