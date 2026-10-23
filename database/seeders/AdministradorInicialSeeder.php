<?php

namespace Database\Seeders;

use App\Enums\Perfil;
use App\Models\AuditoriaLog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Cria o primeiro Administrador de TI.
 *
 * Existe porque os perfis profissionais só nascem por convite, e convites só
 * podem ser emitidos por um administrador: alguém precisa ser o primeiro.
 *
 *  - E-mail e senha vêm de variáveis de ambiente (ADMIN_INICIAL_EMAIL e
 *    ADMIN_INICIAL_SENHA), nunca do código.
 *  - É seguro rodar mais de uma vez: se o e-mail já existe, nada é alterado
 *    (a senha de uma conta existente nunca é sobrescrita).
 *  - Não registra consentimento em nome da pessoa: no primeiro acesso, ela
 *    mesma concede em "Meus dados".
 *  - No primeiro login o sistema exige que ela ative a verificação em duas
 *    etapas, como acontece com todo perfil profissional.
 */
class AdministradorInicialSeeder extends Seeder
{
    public function run(): void
    {
        $nome = config('ecoa.admin_inicial.nome');
        $email = config('ecoa.admin_inicial.email');
        $senha = config('ecoa.admin_inicial.senha');

        if (blank($email) || blank($senha)) {
            $this->command?->warn('Defina ADMIN_INICIAL_EMAIL e ADMIN_INICIAL_SENHA. Nenhum administrador foi criado.');

            return;
        }

        if (User::where('email', $email)->exists()) {
            $this->command?->info('O administrador inicial já existe. Nada a fazer.');

            return;
        }

        $admin = new User();
        $admin->forceFill([
            'name' => $nome,
            'email' => $email,
            'password' => Hash::make($senha),
            'perfil' => Perfil::AdministradorTi->value,
        ])->save();

        AuditoriaLog::registrar('administrador_inicial_criado', ['user_id' => $admin->id]);

        $this->command?->info('Administrador inicial criado. Entre e ative a verificação em duas etapas.');
    }
}
