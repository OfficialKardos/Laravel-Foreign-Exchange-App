<?php
namespace Tests\Unit;

use App\Models\Currency;
use App\Models\Order;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OrderModelTest extends TestCase
{
    use DatabaseTransactions;

    public function test_order_can_be_created()
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

        $this->assertDatabaseHas('orders', [
            'currency_id' => $currency->id,
            'foreign_amount' => 1000,
            'zar_amount' => 80.83,
            'exchange_rate' => 0.0808279000,
            'surcharge_percentage' => 7.50,
            'surcharge_amount' => 6.06,
            'discount_percentage' => 0
        ]);
    }

    public function test_order_belongs_to_currency()
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

        $this->assertInstanceOf(Currency::class, $order->currency);
        $this->assertEquals($currency->id, $order->currency->id);
    }
}