<?php

namespace Saggre\LaravelModelInstance\Services;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Saggre\LaravelModelInstance\Traits\Instantiable;
use Spatie\ModelInfo\Attributes\Attribute;
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

        return $appModels
            ->filter(
                fn($appModelClassPath) => str_contains(class_basename($appModelClassPath), $model)
            )->filter(
                fn($appModelClassPath) => in_array(Instantiable::class, class_uses_recursive($appModelClassPath))
            )
            ->values();
    }

    /**
     * Get options for an attribute.
     *
     * @param Attribute $attribute
     *
     * @return array
     * @throws Exception
     */
    public function getAttributeOptions(Attribute $attribute): array
    {
        if ($attribute->cast && enum_exists($attribute->cast)) {
            $cases = call_user_func([$attribute->cast, 'cases']);

            return array_column($cases, 'value');
        }

        throw new Exception('No options found for attribute');
    }

    /**
     * Filter an attribute value.
     *
     * @param Attribute $attribute
     * @param Model&Instantiable $instance
     * @param mixed $value
     *
     * @return mixed
     */
    public function filterAttributeValue(Attribute $attribute, Model $instance, mixed $value): mixed
    {
        if (method_exists($instance, 'getInstantiationFilters')) {
            $filters = $instance->getInstantiationFilters();
        } else {
            $filters = collect();
        }

        if ($filters->has($attribute->name)) {
            $filter = $filters->get($attribute->name);

            return $filter($value);
        }

        return $value;
    }

    /**
     * Get default value for an attribute.
     *
     * @param Attribute $attribute
     * @param Model&Instantiable $instance
     *
     * @return mixed|null
     */
    public function getAttributeDefaultValue(Attribute $attribute, Model $instance): mixed
    {
        if (method_exists(
                $instance,
                'getInstantiationDefaults'
            ) && $instance->getInstantiationDefaults()->has($attribute->name)) {
            return $instance->getInstantiationDefaults()->get($attribute->name);
        }

        return $attribute->default ?? null;
    }
}
