<?php

namespace Saggre\LaravelModelInstance\Services;

use Illuminate\Support\Collection;
use Spatie\ModelInfo\ModelFinder;

class ModelInstanceCommandService
{
    /**
     * Normalize a model name to a class name (not class path).
     *
     * @param string $model
     *
     * @return string
     */
    public function normalizeModelName(string $model): string
    {
        $ucKeys = '-_';

        $model = str_replace('/', '\\', $model);
        $model = str_replace('\\\\', '\\', $model);
        $model = class_basename($model);
        $model = ucwords($model, $ucKeys);

        return str_replace(str_split($ucKeys), '', $model);
    }

    /**
     * Get the class paths of all app models.
     *
     * @return Collection
     */
    public function getAppModelClassPaths(): Collection
    {
        return ModelFinder::all();
    }

    /**
     * Find class path candidates for the given model name.
     *
     * @param string $model
     *
     * @return Collection
     */
    public function findAppModelCandidateClassPaths(string $model): Collection
    {
        $appModels = $this->getAppModelClassPaths();
        $model     = $this->normalizeModelName($model);

        return $appModels->filter(
            fn($appModelClassPath) => $model === class_basename($appModelClassPath)
        )->values();
    }
}
