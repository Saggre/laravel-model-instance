<?php

namespace Saggre\LaravelModelInstance\Data;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use ReflectionClass;
use Spatie\ModelInfo\Attributes\Attribute;
use Spatie\ModelInfo\ModelInfo;

class ModelInstanceInfo
{
    public function __construct(
        public ModelInfo $modelInfo,
        public Model $instance,
    ) {
    }

    public static function forModel(string|Model|ReflectionClass $model): self
    {
        if ($model instanceof ReflectionClass) {
            $model = $model->getName();
        }

        if (is_string($model)) {
            $model = new $model;
        }

        return new self(ModelInfo::forModel($model), $model);
    }

    public function getHiddenAttributes(): Collection
    {
        return collect(array_merge(
            $modelInstance->instanceHidden ?? [],
            [
                'id',
                'created_at',
                'updated_at',
            ]
        ));
    }

    /**
     * @return Collection
     */
    public function getFillableAttributes(): Collection
    {
        return $this->modelInfo->attributes
            ->filter(
                fn(Attribute $attribute) => collect($this->instance->getFillable())->contains($attribute->name)
            )
            ->filter(
                fn(Attribute $attribute) => ! $this->getHiddenAttributes()->contains($attribute->name)
            );
    }
}
