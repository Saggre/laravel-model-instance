<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Saggre\LaravelModelInstance\Traits\Instantiable;

class Sauce extends Model
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
