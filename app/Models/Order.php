<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    protected $fillable = [
        'member_id',
        'status',
        'total_items',
        'shipping_cost',
        'total',
        'nif',
        'delivery_address',
        'pdf_receipt',
        'cancel_reason',
        'date',
    ];



    // ------------------- CODIGO RELACOES ----------------------
    public function operations(): HasMany
    {
        return $this->hasMany(Operation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_id');
    }


    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'items_orders')->withPivot('quantity', 'unit_price', 'discount', 'subtotal');
    }
}
