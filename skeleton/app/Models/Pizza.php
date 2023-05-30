<?php

namespace Saggre\LaravelModelInstance\Testbench\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pizza extends Model
{
    protected $fillable = [
        'name',
    ];

    public function sauce(): HasOne
    {
        return $this->hasOne(Sauce::class);
    }

    public function toppings(): HasMany
    {
        return $this->hasMany(Topping::class);
    }
}
