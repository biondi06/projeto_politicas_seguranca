<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Requisitos 4.4/4.5/4.6/4.7 — registro explícito de consentimento,
 * associado à finalidade do tratamento de dados, com data e versão
 * do termo aceito, e suporte à revogação (revogado_em).
 */
class Consentimento extends Model
{
    protected $fillable = [
        'user_id',
        'finalidade',
        'versao_termo',
        'aceito_em',
        'revogado_em',
        'ip',
    ];

    protected $casts = [
        'aceito_em' => 'datetime',
        'revogado_em' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}