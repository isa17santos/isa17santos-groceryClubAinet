<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $table = 'settings';

    protected $fillable = ['membership_fee'];

    public $timestamps = false; // tabela só tem um registo
}
