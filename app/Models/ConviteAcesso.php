<?php

namespace App\Models;

use App\Enums\Perfil;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Convite para criar uma conta profissional (fonoaudiólogo, coordenador
 * clínico ou administrador de TI).
 *
 * Regras de um convite:
 *  - vale só para o e-mail informado;
 *  - dá exatamente o perfil escolhido pelo administrador;
 *  - pode ser usado uma única vez;
 *  - expira (por padrão, em 72 horas);
 *  - pode ser revogado enquanto ainda não foi usado.
 */
class ConviteAcesso extends Model
{
    protected $table = 'convites_acesso';

    protected $fillable = [
        'token_hash',
        'email',
        'perfil',
        'criado_por',
        'expira_em',
    ];

    protected $casts = [
        'perfil' => Perfil::class,
        'expira_em' => 'datetime',
        'usado_em' => 'datetime',
        'revogado_em' => 'datetime',
    ];

    /** O que vai para o banco: nunca o token em si. */
    public static function hashDoToken(string $token): string
    {
        return hash('sha256', $token);
    }

    /**
     * Cria um convite e devolve o par [convite, token].
     *
     * O token em texto puro só existe neste retorno: depois de mostrado ao
     * administrador, não há como recuperá-lo (só o hash fica guardado).
     *
     * @return array{0: self, 1: string}
     */
    public static function emitir(string $email, Perfil $perfil, User $criador, int $validadeEmHoras): array
    {
        $token = Str::random(48);

        $convite = static::create([
            'token_hash' => static::hashDoToken($token),
            'email' => static::normalizarEmail($email),
            'perfil' => $perfil,
            'criado_por' => $criador->id,
            'expira_em' => now()->addHours($validadeEmHoras),
        ]);

        return [$convite, $token];
    }

    /** Devolve o convite se o token existir E ainda puder ser usado; senão, null. */
    public static function validoPorToken(?string $token): ?self
    {
        if (blank($token)) {
            return null;
        }

        $convite = static::where('token_hash', static::hashDoToken($token))->first();

        return $convite && $convite->estaDisponivel() ? $convite : null;
    }

    /** pendente | usado | revogado | expirado */
    public function situacao(): string
    {
        return match (true) {
            $this->usado_em !== null => 'usado',
            $this->revogado_em !== null => 'revogado',
            $this->expira_em->isPast() => 'expirado',
            default => 'pendente',
        };
    }

    public function estaDisponivel(): bool
    {
        return $this->situacao() === 'pendente';
    }

    /** O e-mail digitado no cadastro é o mesmo para o qual o convite foi emitido? */
    public function emailConfere(string $email): bool
    {
        return static::normalizarEmail($email) === $this->email;
    }

    public function criador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criado_por');
    }

    private static function normalizarEmail(string $email): string
    {
        return mb_strtolower(trim($email));
    }
}
