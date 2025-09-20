<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'currency_id' => 'required|exists:currencies,id',
            'currency_code' => 'required|string',
            'foreign_amount' => 'required|numeric|min:0',
            'zar_amount' => 'required|numeric|min:0',
            'exchange_rate' => 'required|numeric|min:0',
            'surcharge_percentage' => 'required|numeric|min:0',
            'surcharge_amount' => 'required|numeric|min:0'
        ]);

        if ($validated['currency_code'] == 'EUR') {
            $validated['discount_percentage'] = env('EUR_DISCOUNT', 0);
        } else {
            $validated['discount_percentage'] = 0;
        }

        try {
            $order = Order::create($validated);
            
            return response()->json([
                'success' => true,
                'data' => $order
            ]);
            
        } catch (\Exception $e) {
            Log::error('Order creation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error_msg' => 'Failed to place order.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}