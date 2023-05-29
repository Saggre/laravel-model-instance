<?php

namespace Saggre\LaravelModelInstance\Testbench\App\Models;

use Illuminate\Database\Eloquent\Model;

class Pizza extends Model
{
    protected $fillable = [
        'name',
    ];

    public function sauce()
    {
        return $this->hasOne(Sauce::class);
    }

    public function toppings()
    {
        return $this->hasMany(Topping::class);
    }
}
