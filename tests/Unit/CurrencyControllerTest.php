<?php
namespace Tests\Unit;

use App\Http\Controllers\CurrencyController;
use App\Models\Currency;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CurrencyControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_index_returns_all_currencies()
    {
        Currency::truncate();
        
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

        $controller = new CurrencyController();
        $response = $controller->index();

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);

        $this->assertTrue($responseData['success']);
        $this->assertCount(3, $responseData['data']);
        
        $this->assertArrayHasKey('data', $responseData);
        $this->assertTrue(is_array($responseData['data']));
        
        foreach ($responseData['data'] as $currency) {
            $this->assertArrayHasKey('id', $currency);
            $this->assertArrayHasKey('name', $currency);
            $this->assertArrayHasKey('code', $currency);
            $this->assertArrayHasKey('exchange_rate', $currency);
            $this->assertArrayHasKey('surcharge_percentage', $currency);
        }
    }
}