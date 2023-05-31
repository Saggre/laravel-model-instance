<?php

namespace Saggre\LaravelModelInstance\Testbench\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Saggre\LaravelModelInstance\Traits\Instantiable;

class Pizza extends Model
{
    use Instantiable;

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
