<?php

namespace Saggre\LaravelModelInstance\Testbench\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Saggre\LaravelModelInstance\Traits\Instantiable;

class Topping extends Model
{
    use Instantiable;

    protected $fillable = [
        'name',
    ];

    public function pizza(): BelongsTo
    {
        return $this->belongsTo(Pizza::class);
    }
}
