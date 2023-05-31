<?php

namespace Saggre\LaravelModelInstance\Testbench\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sauce extends Model
{
    protected $fillable = [
        'name',
    ];

    public function pizza(): BelongsTo
    {
        return $this->belongsTo(Pizza::class);
    }
}
