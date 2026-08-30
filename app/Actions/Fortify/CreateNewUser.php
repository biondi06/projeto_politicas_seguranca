<?php

namespace App\Actions\Fortify;

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
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */

    
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
        ])->validate();

        return User::create([
            'name' => $input['name'],
            'perfil' => $input['perfil'],
            'email' => $input['email'],
            // A senha nunca é armazenada em texto puro.
            // Hash::make() utiliza o algoritmo configurado pela aplicação,
            // atualmente Argon2id, gerando um hash com salt criptográfico
            // exclusivo para cada senha.
            'password' => Hash::make($input['password']),
        ]);
    }
}
