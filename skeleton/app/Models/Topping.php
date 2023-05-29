<?php

namespace Saggre\LaravelModelInstance\Testbench\App\Models;

use Illuminate\Database\Eloquent\Model;

class Topping extends Model
{
    protected $fillable = [
        'name',
    ];

    public function pizza()
    {
        return $this->belongsTo(Pizza::class);
    }
}
