<?php

namespace Saggre\LaravelModelInstance\Tests;

use Saggre\LaravelModelInstance\Console\ModelInstanceCommand;
use Saggre\LaravelModelInstance\ModelInstanceServiceProvider;

class TestModelInstanceServiceProvider extends ModelInstanceServiceProvider
{
    protected function registerBindings()
    {
        $this->app->singleton('command.model-instance', function () {
            return new ModelInstanceCommand(
                new TestModelInstanceCommandService()
            );
        });

        $this->app->alias('command.model-instance', ModelInstanceCommand::class);
    }
}