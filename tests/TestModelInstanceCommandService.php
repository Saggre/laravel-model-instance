<?php

namespace Saggre\LaravelModelInstance\Tests;

use Illuminate\Support\Collection;
use Saggre\LaravelModelInstance\Services\ModelInstanceCommandService;
use Spatie\ModelInfo\ModelFinder;

class TestModelInstanceCommandService extends ModelInstanceCommandService
{
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
            ''
        );
    }
}
