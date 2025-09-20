<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'currency_id',
        'exchange_rate',
        'surcharge_percentage',
        'discount_percentage',
        'foreign_amount',
        'zar_amount',
        'surcharge_amount',
    ];

    public $timestamps = true;

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }
}
