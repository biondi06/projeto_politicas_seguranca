<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResponsavelLegal extends Model
{
    protected $fillable = [
    'usuario_id',
    'nome',
    'contato',
    'email',
    ];
}
