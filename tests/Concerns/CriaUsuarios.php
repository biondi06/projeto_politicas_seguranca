<?php

namespace Tests\Concerns;

use App\Enums\Perfil;
use App\Models\Consentimento;
use App\Models\User;

/**
 * Atalhos para os testes criarem usuários já aptos a usar o sistema.
 */
trait CriaUsuarios
{
    /** Profissional com a verificação em duas etapas ativa e o consentimento dado. */
    protected function profissional(Perfil $perfil): User
    {
        $usuario = User::factory()->create();

        $usuario->forceFill([
            'perfil' => $perfil->value,
            'two_factor_secret' => encrypt('SEGREDO-DE-TESTE'),
            'two_factor_confirmed_at' => now(),
        ])->save();

        $this->darConsentimento($usuario);

        return $usuario;
    }

    /** Responsável legal com o consentimento dado (esse perfil não exige 2FA). */
    protected function responsavel(): User
    {
        $usuario = User::factory()->create();
        $usuario->forceFill(['perfil' => Perfil::ResponsavelLegal->value])->save();

        $this->darConsentimento($usuario);

        return $usuario;
    }

    private function darConsentimento(User $usuario): void
    {
        Consentimento::create([
            'user_id' => $usuario->id,
            'finalidade' => 'Uso da plataforma Ecoa',
            'versao_termo' => 'v1.0',
            'aceito_em' => now(),
        ]);
    }
}
