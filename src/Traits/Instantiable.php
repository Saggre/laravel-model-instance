<?php

namespace Saggre\LaravelModelInstance\Traits;

use Illuminate\Support\Collection;

trait Instantiable
{
    /**
     * Get properties that can be instantiated.
     *
     * @return Collection<string>
     */
    public function getInstantiableProperties(): Collection
    {
        return collect();
    }

    /**
     * Get default values for properties that can be instantiated.
     *
     * @return Collection
     */
    public function getInstantiationDefaults(): Collection
    {
        return collect();
    }
}
