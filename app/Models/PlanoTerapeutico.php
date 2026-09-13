<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanoTerapeutico extends Model
{
   protected $fillable = [
    'crianca_id',
    'fonoaudiologo_responsavel_id',
    'tipo_dificuldade',
    'fonemas_alvo',
    'metas',
    'status',
    'data_criacao',
    'data_atualizacao',
];

    protected $casts = [
    'tipo_dificuldade' => 'encrypted',
    'fonemas_alvo' => 'encrypted',
    'metas' => 'encrypted',

];
}
