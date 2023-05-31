<?php

namespace Saggre\LaravelModelInstance\Testbench\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Topping extends Model
{
    protected $fillable = [
        'name',
    ];

    public function pizza(): BelongsTo
    {
        return $this->belongsTo(Pizza::class);
    }
}
