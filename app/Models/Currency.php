<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Currency extends Model
{
    use HasFactory;

    protected $table = 'currencies';

    protected $fillable = [
        'code',
        'name',
        'exchange_rate',
        'surcharge_percentage',
        'discount_percentage',
        'last_updated',
    ];

    public $timestamps = false;
    public function orders()
    {
        return $this->hasMany(Order::class, 'currency_id');
    }

    public static function updateCurrencies($code, $rate)
    {
        $currency = self::where('code', $code)->first();
        
        if ($currency) {
            $currency->exchange_rate = $rate;
            $currency->save();
            return true;
        }
        
        return false;
    }

}
