<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Card extends Model
{
    // TO DO ....
    

    // ------------------- CODIGO RELACOES ----------------------
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id', 'id');
    }

    public function operations(): HasMany
    {
        return $this->hasMany(Operation::class);
    }
}
