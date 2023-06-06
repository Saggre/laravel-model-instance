<?php

namespace Saggre\LaravelModelInstance\Testbench\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Saggre\LaravelModelInstance\Testbench\App\Enums\PizzaTypeEnum;
use Saggre\LaravelModelInstance\Traits\Instantiable;

class Pizza extends Model
{
    use Instantiable;

    protected $fillable = [
        'name',
        'crust',
    ];

    protected $casts = [
        'crust' => PizzaTypeEnum::class,
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
