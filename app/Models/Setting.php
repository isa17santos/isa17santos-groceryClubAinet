<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public $timestamps = true; // Se usas os campos created_at e updated_at
    protected $table = 'settings';
}
