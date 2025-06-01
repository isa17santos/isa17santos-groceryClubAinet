<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplyOrder extends Model
{
    protected $fillable = ['product_id', 'registered_by_user_id', 'status', 'quantity'];

    // ------------------- CODIGO RELACOES ----------------------
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by_user_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
