<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Requisito 5 — Auditoria e Logs.
 *
 * Cada registro guarda o hash do registro anterior (hash_anterior) e
 * o próprio hash calculado (hash_atual), formando uma cadeia (blockchain
 * simplificada): alterar qualquer campo de um registro antigo muda seu
 * hash, quebrando a cadeia a partir dali — o que permite DETECTAR
 * adulteração via verificarIntegridade() (requisito 5.3).
 */
class AuditoriaLog extends Model
{
    protected $table = 'logs_auditorias';
    public $timestamps = false;

    protected $fillable = [
        'evento', 'user_id', 'email', 'ip', 'detalhes',
        'hash_anterior', 'hash_atual', 'created_at',
    ];

    protected $casts = [
        'detalhes' => 'array',
        'created_at' => 'datetime',
    ];

    public static function registrar(string $evento, array $dados = []): self
    {
        $ultimo = static::orderByDesc('id')->first();
        $hashAnterior = $ultimo->hash_atual ?? str_repeat('0', 64);
        $agora = now();

        $payload = [
            'evento' => $evento,
            'user_id' => $dados['user_id'] ?? null,
            'email' => $dados['email'] ?? null,
            'ip' => request()->ip(),
            'detalhes' => $dados['detalhes'] ?? null,
            'timestamp' => $agora->toIso8601String(),
        ];

        $hashAtual = hash('sha256', $hashAnterior . json_encode($payload));

        return static::create([
            'evento' => $evento,
            'user_id' => $dados['user_id'] ?? null,
            'email' => $dados['email'] ?? null,
            'ip' => request()->ip(),
            'detalhes' => $dados['detalhes'] ?? null,
            'hash_anterior' => $hashAnterior,
            'hash_atual' => $hashAtual,
            'created_at' => $agora,
        ]);
    }

    /**
     * Percorre toda a cadeia recalculando os hashes — se algum registro
     * foi alterado manualmente no banco, o hash recalculado não bate
     * mais com o armazenado, e a adulteração é detectada.
     */
    public static function verificarIntegridade(): array
    {
        $anterior = str_repeat('0', 64);
        $total = 0;
        $adulterado = null;

        foreach (static::orderBy('id')->cursor() as $log) {
            $payload = [
                'evento' => $log->evento,
                'user_id' => $log->user_id,
                'email' => $log->email,
                'ip' => $log->ip,
                'detalhes' => $log->detalhes,
                'timestamp' => $log->created_at->toIso8601String(),
            ];
            $hashCalculado = hash('sha256', $anterior . json_encode($payload));

            if ($hashCalculado !== $log->hash_atual) {
                $adulterado = $log->id;
                break;
            }
            $anterior = $log->hash_atual;
            $total++;
        }

        return ['total' => $total, 'adulterado' => $adulterado];
    }
}