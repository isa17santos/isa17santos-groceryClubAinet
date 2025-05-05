<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Operation extends Model
{
    // TO DO ...


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
