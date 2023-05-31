<?php

namespace Saggre\LaravelModelInstance\Traits;

use Illuminate\Support\Collection;

trait Instantiable
{
    protected $uninstantiable = [
        'id',
        'created_at',
        'updated_at',
    ];

    /**
     * Get properties that can't or don't need to be instantiated.
     *
     * @return Collection<string>
     */
    public function getUninstantiable(): Collection
    {
        return collect($this->uninstantiable);
    }
}
