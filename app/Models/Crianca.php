<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Crianca extends Model
{
    protected $fillable = [
    'nome',
    'data_nascimento',
    'responsavel_legal_id',
    ];
}
