<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CurrencyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->currencyCode,
            'code' => $this->faker->unique()->currencyCode,
            'exchange_rate' => $this->faker->randomFloat(8, 0.01, 100),
            'surcharge_percentage' => $this->faker->randomFloat(2, 0, 10),
        ];
    }
}