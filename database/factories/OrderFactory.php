<?php

namespace Database\Factories;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        $currency = Currency::factory()->create();
        
        return [
            'currency_id' => $currency->id,
            'foreign_amount' => $this->faker->randomFloat(2, 100, 10000),
            'zar_amount' => $this->faker->randomFloat(2, 100, 10000),
            'exchange_rate' => $currency->exchange_rate,
            'surcharge_percentage' => $currency->surcharge_percentage,
            'surcharge_amount' => $this->faker->randomFloat(2, 10, 1000),
            'discount_percentage' => 0,
        ];
    }
}