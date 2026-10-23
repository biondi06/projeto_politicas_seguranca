<?php

namespace App\Actions\Fortify;

use App\Enums\Perfil;
use App\Models\AuditoriaLog;
use App\Models\Consentimento;
use App\Models\ConviteAcesso;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

/**
 * Cadastro de novos usuários.
 *
 * REGRA DE SEGURANÇA: o perfil de acesso NUNCA vem do formulário.
 *  - Cadastro aberto (sem convite): a conta nasce como Responsável Legal.
 *  - Cadastro com convite: a conta nasce com o perfil que o administrador
 *    escolheu ao emitir o convite, e só se o e-mail digitado for o do convite.
 *
 * Antes dessa regra, o formulário enviava o perfil e o servidor o aceitava, o
 * que permitia a qualquer visitante se cadastrar como Administrador de TI.
 */
class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /** Mantida igual à finalidade usada em LgpdController (concessão de novo consentimento). */
    private const FINALIDADE_CONSENTIMENTO = 'Uso da plataforma Ecoa e tratamento de dados terapêuticos da criança acompanhada';

    /**
     * @param  array<string, mixed>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'password' => $this->passwordRules(),
            // Requisito 4.4: consentimento explícito e obrigatório.
            'aceite_lgpd' => ['required', 'accepted'],
        ], [
            'aceite_lgpd.required' => 'Leia os Termos de Uso até o final e aceite para criar a conta.',
            'aceite_lgpd.accepted' => 'Leia os Termos de Uso até o final e aceite para criar a conta.',
        ])->validate();

        // Tudo numa transação: se algo falhar no meio (convite inválido, por
        // exemplo), nada é gravado, e o convite só é "gasto" se a conta nascer.
        return DB::transaction(function () use ($input) {
            $convite = $this->localizarConvite($input);
            $perfil = $convite ? $convite->perfil : Perfil::doCadastroAberto();

            $usuario = new User();
            $usuario->forceFill([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
                'perfil' => $perfil->value,
            ])->save();

            if ($convite) {
                $convite->forceFill([
                    'usado_em' => now(),
                    'usado_por' => $usuario->id,
                ])->save();

                AuditoriaLog::registrar('convite_usado', [
                    'user_id' => $usuario->id,
                    'detalhes' => ['perfil' => $perfil->value],
                ]);
            }

            // Requisitos 4.4, 4.5 e 4.7: finalidade, data e versão do termo aceito.
            Consentimento::create([
                'user_id' => $usuario->id,
                'finalidade' => self::FINALIDADE_CONSENTIMENTO,
                'versao_termo' => config('ecoa.termos_versao', 'v1.0'),
                'aceito_em' => now(),
                'ip' => request()->ip(),
            ]);

            return $usuario;
        });
    }

    /**
     * Procura o convite informado no cadastro. Sem convite, devolve null
     * (cadastro aberto). Com convite, ele precisa existir, estar disponível e
     * ser para o e-mail digitado; do contrário o cadastro é recusado.
     *
     * A mensagem é a mesma para qualquer motivo de recusa, para não ensinar a
     * quem está tentando adivinhar o que existe ou não.
     *
     * @param  array<string, mixed>  $input
     */
    private function localizarConvite(array $input): ?ConviteAcesso
    {
        $token = $input['convite'] ?? null;

        if (blank($token)) {
            return null;
        }

        // lockForUpdate: duas pessoas usando o mesmo convite ao mesmo tempo
        // não passam as duas; a segunda espera e encontra o convite já usado.
        $convite = ConviteAcesso::where('token_hash', ConviteAcesso::hashDoToken((string) $token))
            ->lockForUpdate()
            ->first();

        $valido = $convite
            && $convite->estaDisponivel()
            && $convite->emailConfere($input['email']);

        if (! $valido) {
            throw ValidationException::withMessages([
                'convite' => 'Este convite é inválido, expirou ou não corresponde ao e-mail informado.',
            ]);
        }

        return $convite;
    }
}
