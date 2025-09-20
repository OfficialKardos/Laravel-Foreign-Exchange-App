<?php

namespace Tests\Unit;

use App\Http\Controllers\OrderController;
use App\Models\Currency;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_store_creates_order_and_returns_success_response()
    {
        $currency = Currency::create([
            'name' => 'US Dollar',
            'code' => 'USD',
            'exchange_rate' => 0.0808279000,
            'surcharge_percentage' => 7.50
        ]);

        $requestData = [
            'currency_id' => $currency->id,
            'currency_code' => 'USD',
            'foreign_amount' => 1000,
            'zar_amount' => 80.83,
            'exchange_rate' => 0.0808279000,
            'surcharge_percentage' => 7.50,
            'surcharge_amount' => 6.06
        ];

        $request = Request::create('/api/orders', 'POST', $requestData);
        
        $controller = new OrderController();
        $response = $controller->store($request);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);

        $this->assertTrue($responseData['success']);
        $this->assertArrayHasKey('data', $responseData);

        $this->assertDatabaseHas('orders', [
            'currency_id' => $currency->id,
            'foreign_amount' => '1000.00',
            'zar_amount' => '80.83',
            'discount_percentage' => '0.00'
        ]);
    }

    public function test_store_applies_eur_discount()
    {
        $currency = Currency::create([
            'name' => 'Euro',
            'code' => 'EUR',
            'exchange_rate' => 1.10,
            'surcharge_percentage' => 6.00
        ]);

        putenv('EUR_DISCOUNT=5.00');

        $requestData = [
            'currency_id' => $currency->id,
            'currency_code' => 'EUR',
            'foreign_amount' => 1000,
            'zar_amount' => 1100.00,
            'exchange_rate' => 1.10,
            'surcharge_percentage' => 6.00,
            'surcharge_amount' => 66.00
        ];

        $request = Request::create('/api/orders', 'POST', $requestData);
        
        $controller = new OrderController();
        $response = $controller->store($request);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);

        $this->assertTrue($responseData['success']);

        echo "\nActual response data: " . json_encode($responseData) . "\n";

        $this->assertDatabaseHas('orders', [
            'currency_id' => $currency->id,
            'foreign_amount' => '1000.00',
            'zar_amount' => '1100.00',
            'discount_percentage' => '0.02'
        ]);
    }
}