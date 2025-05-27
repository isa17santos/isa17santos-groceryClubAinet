<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingCostSetting extends Model
{
    protected $table = 'settings_shipping_costs';

    protected $fillable = ['min_value_threshold', 'max_value_threshold', 'shipping_cost'];

    public $timestamps = false;

    public static function getCostForOrderTotal(float $orderTotal): float
    {
        return self::where('min_value_threshold', '<=', $orderTotal)
            ->where('max_value_threshold', '>', $orderTotal)
            ->value('shipping_cost') ?? 0;
    }
}
