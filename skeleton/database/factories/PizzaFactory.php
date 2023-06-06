<?php

namespace Saggre\LaravelModelInstance\Testbench\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Saggre\LaravelModelInstance\Testbench\App\Enums\PizzaTypeEnum;
use Saggre\LaravelModelInstance\Testbench\App\Models\Pizza;

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
