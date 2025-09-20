<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Currency;
use Illuminate\Support\Facades\Log;

class UpdateExchangeRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exchange:update-rates {action : The action to perform (run)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update currency exchange rates from API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'run':
                $this->updateRates();
                break;
            default:
                $this->error("Exchange Rate Api");
                return 1;
        }
    }

    private function updateRates()
    {
        $apiKey = config('services.json_rates.key');
        
        if (empty($apiKey)) {
            $this->error('JSON_RATES_KEY not configured in .env file');
            return 1;
        }

        $url = "http://apilayer.net/api/live?access_key={$apiKey}&currencies=USD,GBP,EUR,KES&source=ZAR&format=1";

        try {
            $response = Http::get($url);
            
            if (!$response->successful()) {
                $this->error("Failed to fetch exchange rates: " . $response->status());
                return 1;
            }

            $data = $response->json();

            if (!$data['success']) {
                $this->error("API error: " . json_encode($data));
                return 1;
            }
            
            $usdToZar = 1 / $data['quotes']['ZARUSD']; // 1 USD in ZAR
            $gbpToZar = 1 / $data['quotes']['ZARGBP']; // 1 GBP in ZAR
            $eurToZar = 1 / $data['quotes']['ZAREUR']; // 1 EUR in ZAR
            $kesToZar = 1 / $data['quotes']['ZARKES']; // 1 KES in ZAR

            $this->info("1 USD = {$usdToZar} ZAR");
            $this->info("1 GBP = {$gbpToZar} ZAR");
            $this->info("1 EUR = {$eurToZar} ZAR");
            $this->info("1 KES = {$kesToZar} ZAR");

            $rates = [
                'USD' => $usdToZar,
                'GBP' => $gbpToZar,
                'EUR' => $eurToZar,
                'KES' => $kesToZar,
            ];

            foreach ($rates as $code => $rate) {
                $this->updateCurrency($code, $rate);
            }

            $this->info("Exchange rates updated successfully!");
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Error updating exchange rates: " . $e->getMessage());
            Log::error('Exchange rates update failed: ' . $e->getMessage());
            return 1;
        }
    }

    private function updateCurrency($code, $rate)
    {
        $currency = Currency::where('code', $code)->first();
        
        if ($currency) {
            $currency->exchange_rate = $rate;
            $currency->save();
            $this->info("Updated {$code} rate to {$rate}");
        } else {
            $this->warn("Currency {$code} not found in database");
        }
    }
}