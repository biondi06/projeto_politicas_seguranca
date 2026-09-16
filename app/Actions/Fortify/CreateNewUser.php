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

    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'perfil' => ['required', 'string', 'in:fonoaudiologo,coordenador_clinico,administrador_ti,responsavel_legal'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
            // Requisito 4.4 — consentimento explícito obrigatório no cadastro
            'aceite_lgpd' => ['required', 'accepted'],
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'perfil' => $input['perfil'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        // Requisitos 4.4/4.5/4.7 — registra o consentimento, associado
        // à finalidade do tratamento, com data e versão do termo aceito.
        Consentimento::create([
            'user_id' => $user->id,
            'finalidade' => 'Uso da plataforma Ecoa e tratamento de dados terapêuticos da criança acompanhada',
            'versao_termo' => 'v1.0',
            'aceito_em' => now(),
            'ip' => request()->ip(),
        ]);

        return $user;
    }
}