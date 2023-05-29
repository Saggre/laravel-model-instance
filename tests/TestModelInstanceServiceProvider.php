<?php

namespace Saggre\LaravelModelInstance\Tests;

use Illuminate\Support\Collection;
use Saggre\LaravelModelInstance\Console\ModelInstanceCommand;
use Saggre\LaravelModelInstance\ModelInstanceServiceProvider;
use Saggre\LaravelModelInstance\Services\ModelInstanceCommandService;
use Spatie\ModelInfo\ModelFinder;

class TestModelInstanceServiceProvider extends ModelInstanceServiceProvider
{
    protected function registerBindings()
    {
        $this->app->singleton('command.model-instance', function () {
            return new ModelInstanceCommand(
                new class extends ModelInstanceCommandService {
                    /**
                     * Get the class paths of all test bench models.
                     *
                     * @return Collection
                     */
                    public function getAppModelClassPaths(): Collection
                    {
                        return ModelFinder::all(
                            null,
                            null,
                            'Saggre\\LaravelModelInstance\\Testbench'
                        );
                    }
                }
            );
        });

        $this->app->alias('command.model-instance', ModelInstanceCommand::class);
    }
}