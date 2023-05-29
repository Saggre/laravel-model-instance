<?php

namespace Saggre\LaravelModelInstance\Testbench\App\Models;

use Illuminate\Database\Eloquent\Model;

class Sauce extends Model
{
    protected $fillable = [
        'name',
    ];

    public function pizza()
    {
        return $this->belongsTo(Pizza::class);
    }
}
