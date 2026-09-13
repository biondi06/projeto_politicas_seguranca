<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistroEvolucao extends Model
{
    protected $casts = [
    'observacoes' => 'encrypted',
    ];  
}
