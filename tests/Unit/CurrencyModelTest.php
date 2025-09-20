<?php
namespace Tests\Unit;

use App\Models\Currency;
use App\Models\Order;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CurrencyModelTest extends TestCase
{
    use DatabaseTransactions;

    public function test_currency_can_be_created()
    {
        $currency = Currency::create([
            'name' => 'US Dollar',
            'code' => 'USD',
            'exchange_rate' => 0.0808279000,
            'surcharge_percentage' => 7.50
        ]);

        $this->assertDatabaseHas('currencies', [
            'name' => 'US Dollar',
            'code' => 'USD',
            'exchange_rate' => 0.0808279000,
            'surcharge_percentage' => 7.50
        ]);
    }

    public function test_currency_has_many_orders()
    {
        $currency = Currency::create([
            'name' => 'US Dollar',
            'code' => 'USD',
            'exchange_rate' => 0.0808279000,
            'surcharge_percentage' => 7.50
        ]);
        
        $order = Order::create([
            'currency_id' => $currency->id,
            'foreign_amount' => 1000,
            'zar_amount' => 80.83,
            'exchange_rate' => 0.0808279000,
            'surcharge_percentage' => 7.50,
            'surcharge_amount' => 6.06,
            'discount_percentage' => 0
        ]);

        $this->assertTrue($currency->orders->contains($order));
        $this->assertEquals(1, $currency->orders->count());
    }
}