<?php

namespace Saggre\LaravelModelInstance\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

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

    /**
     * Get filters for property values that can be instantiated.
     *
     * @return Collection
     */
    public function getInstantiationFilters(): Collection
    {
        return collect([
            'password' => fn($value) => Hash::make($value),
        ]);
    }
}
