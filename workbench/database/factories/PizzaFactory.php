<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\PizzaTypeEnum;
use App\Models\Pizza;

/**
 * @extends Factory<Pizza>
 */
class PizzaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'     => $this->faker->name(),
            'password' => '12345678',
            'crust'    => PizzaTypeEnum::PAN_PIZZA,
        ];
    }
}
