<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Currency;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            ['name' => 'US Dollar', 'code' => 'USD', 'exchange_rate' => 0.0808279, 'surcharge_percentage' => 7.50],
            ['name' => 'British Pound', 'code' => 'GBP', 'exchange_rate' => 0.0527032, 'surcharge_percentage' => 5.00],
            ['name' => 'Euro', 'code' => 'EUR', 'exchange_rate' => 0.0718710, 'surcharge_percentage' => 6.00],
            ['name' => 'Kenyan Shilling', 'code' => 'KES', 'exchange_rate' => 7.81498, 'surcharge_percentage' => 8.00],
        ];

        foreach ($currencies as $currency) {
            Currency::create($currency);
        }
    }
}