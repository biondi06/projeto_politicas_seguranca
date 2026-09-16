<?php

namespace App\Actions\Fortify;

use App\Models\Consentimento;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param array<string, string> $input
     *
     * @throws ValidationException
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'perfil' => [
                'required',
                'string',
                'in:fonoaudiologo,coordenador_clinico,administrador_ti,responsavel_legal',
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],

            'password' => $this->passwordRules(),

            /*
             * Requisitos 4.14 e 4.15
             *
             * O consentimento precisa ser explícito e estar
             * associado à finalidade informada ao titular.
             */
            'aceite_lgpd' => [
                'required',
                'accepted',
            ],
        ])->validate();

        /*
         * Criação do usuário.
         *
         * A senha nunca é armazenada em texto puro.
         * Hash::make() utiliza o algoritmo configurado
         * pela aplicação, atualmente Argon2id.
         */
        $user = User::create([
            'name' => $input['name'],
            'perfil' => $input['perfil'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        /*
         * Registro do consentimento.
         *
         * Requisitos atendidos:
         *
         * 4.14 — Registro explícito de consentimento
         * 4.15 — Consentimento associado à finalidade
         * 4.17 — Registro de data e versão do consentimento
         *
         * O IP é armazenado como evidência adicional do aceite.
         */
        Consentimento::create([
            'user_id' => $user->id,

            'finalidade' =>
                'Uso da plataforma Ecoa e tratamento de dados necessários à execução das funcionalidades do sistema',

            'versao_termo' => 'v1.0',

            'aceito_em' => now(),

            'ip' => request()->ip(),
        ]);

        return $user;
    }
}