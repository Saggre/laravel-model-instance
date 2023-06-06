<?php

namespace Saggre\LaravelModelInstance\Testbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Saggre\LaravelModelInstance\Testbench\App\Enums\PizzaTypeEnum;
use Saggre\LaravelModelInstance\Traits\Instantiable;

class Pizza extends Model
{
    use Instantiable;
    use HasFactory;

    protected $fillable = [
        'name',
        'crust',
        'password',
        'has_sauce',
        'has_mayo',
    ];

    protected $casts = [
        'crust' => PizzaTypeEnum::class,
    ];

    public function getInstantiableProperties(): Collection
    {
        return collect(['has_sauce']);
    }

    public function sauce(): HasOne
    {
        return $this->hasOne(Sauce::class);
    }

    public function toppings(): HasMany
    {
        return $this->hasMany(Topping::class);
    }
}
