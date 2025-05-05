<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Product extends Model
{
    // TO DO ...


    // ------------------- CODIGO RELACOES ----------------------
    public function stockAdjustments(): HasMany
    {
        return $this->hasMany(StockAdjustment::class);
    }


    public function supplyOrders(): HasMany
    {
        return $this->hasMany(SupplyOrder::class);
    }


    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }


    public function orders()
    {
        return $this->belongsToMany(Order::class, 'items_orders')
            ->withPivot('quantity', 'unit_price', 'discount', 'subtotal');
    }
}
