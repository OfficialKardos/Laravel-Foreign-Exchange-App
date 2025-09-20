<?php
namespace Tests\Feature;

use App\Models\Currency;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CurrencyApiTest extends TestCase
{
    use DatabaseTransactions;

    public function test_can_get_all_currencies()
    {
        // Create test currencies with static data
        Currency::create([
            'name' => 'US Dollar',
            'code' => 'USD',
            'exchange_rate' => 0.0808279000,
            'surcharge_percentage' => 7.50
        ]);
        
        Currency::create([
            'name' => 'British Pound',
            'code' => 'GBP',
            'exchange_rate' => 1.25,
            'surcharge_percentage' => 5.00
        ]);
        
        Currency::create([
            'name' => 'Euro',
            'code' => 'EUR',
            'exchange_rate' => 1.10,
            'surcharge_percentage' => 6.00
        ]);
        
        $response = $this->getJson('/api/currencies');
        
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => [
                             'id',
                             'name',
                             'code',
                             'exchange_rate',
                             'surcharge_percentage',
                             'created_at',
                             'updated_at'
                         ]
                     ]
                 ]);
    }
}