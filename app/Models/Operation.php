<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Operation extends Model
{
    use HasFactory;

    protected $fillable = [
        'card_id',
        'type',
        'value',
        'date',
        'credit_type',
        'payment_type',
        'payment_reference',
        'debit_type',
        'order_id',
    ];

    protected $casts = [
        'date' => 'datetime',
        'value' => 'decimal:2',
    ];

    // ------------------- CODIGO RELACOES ----------------------
    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }


    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
