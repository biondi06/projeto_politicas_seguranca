<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistroExercicioRealizado extends Model
{
    protected $casts = [
    'observacao' => 'encrypted',
    ];
}
