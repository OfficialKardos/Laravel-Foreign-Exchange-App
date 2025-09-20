<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('currencies')->insert([
            [
                'code' => 'USD',
                'name' => 'US Dollar',
                'exchange_rate' => 0.0808279,
                'surcharge_percentage' => 7.5,
                'discount_percentage' => 0,
                'last_updated' => now(),
            ],
            [
                'code' => 'GBP',
                'name' => 'British Pound',
                'exchange_rate' => 0.0527032,
                'surcharge_percentage' => 5,
                'discount_percentage' => 0,
                'last_updated' => now(),
            ],
            [
                'code' => 'EUR',
                'name' => 'Euro',
                'exchange_rate' => 0.0718710,
                'surcharge_percentage' => 5,
                'discount_percentage' => 2,
                'last_updated' => now(),
            ],
            [
                'code' => 'KES',
                'name' => 'Kenyan Shilling',
                'exchange_rate' => 7.81498,
                'surcharge_percentage' => 2.5,
                'discount_percentage' => 0,
                'last_updated' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('currencies')->whereIn('code', ['USD','GBP','EUR','KES'])->delete();
    }
};
