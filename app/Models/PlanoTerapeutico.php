<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanoTerapeutico extends Model
{
    protected $casts = [
    'tipo_dificuldade' => 'encrypted',
    'fonemas_alvo' => 'encrypted',
    'metas' => 'encrypted',
];
}
