<?php
namespace Tests\Feature;

use App\Models\Currency;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use DatabaseTransactions;

    public function test_can_create_order()
    {
        $currency = Currency::create([
            'name' => 'US Dollar',
            'code' => 'USD',
            'exchange_rate' => 0.0808279000,
            'surcharge_percentage' => 7.50
        ]);
        
        $orderData = [
            'currency_id' => $currency->id,
            'currency_code' => 'USD',
            'foreign_amount' => 1000,
            'zar_amount' => 80.83,
            'exchange_rate' => 0.0808279000,
            'surcharge_percentage' => 7.50,
            'surcharge_amount' => 6.06
        ];
        
        $response = $this->postJson('/api/orders', $orderData);
        
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'id',
                         'currency_id',
                         'foreign_amount',
                         'zar_amount',
                         'exchange_rate',
                         'surcharge_percentage',
                         'surcharge_amount',
                         'discount_percentage',
                         'created_at',
                         'updated_at'
                     ]
                 ]);
        
        $this->assertDatabaseHas('orders', [
            'currency_id' => $currency->id,
            'foreign_amount' => 1000,
            'zar_amount' => 80.83,
            'discount_percentage' => 0
        ]);
    }
}