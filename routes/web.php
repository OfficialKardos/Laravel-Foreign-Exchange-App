<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\OrderController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/currencies', [CurrencyController::class, 'index']);
Route::post('/api/orders', [OrderController::class, 'store']);


