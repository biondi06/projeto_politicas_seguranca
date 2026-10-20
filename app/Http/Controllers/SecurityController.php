<?php

namespace App\Http\Controllers;

use App\Models\AuditoriaLog;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;

/**
 * Painel de segurança da conta (requisitos 1.5 e 1.6 — 2FA).
 */
class SecurityController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // O segredo do 2FA é guardado criptografado com a APP_KEY. Se a chave
        // foi trocada depois da ativação, o segredo não pode mais ser lido e
        // o QR code falha com "The MAC is invalid" (erro 500). Nesse caso o
        // segredo é inútil: zeramos as colunas do 2FA para o usuário ativar de
        // novo, já com a chave atual. Não abre brecha: um segredo ilegível não
        // valida nenhum código, e perfis profissionais continuam obrigados a
        // reativar o 2FA pelo middleware EnsureTwoFactorIsEnabled.
        if ($user->two_factor_secret) {
            try {
                decrypt($user->two_factor_secret);
            } catch (DecryptException $e) {
                $user->forceFill([
                    'two_factor_secret' => null,
                    'two_factor_recovery_codes' => null,
                    'two_factor_confirmed_at' => null,
                ])->save();

                AuditoriaLog::registrar('2fa_reiniciado_segredo_invalido', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);

                return redirect()
                    ->route('security.index')
                    ->with('status', 'A configuração anterior da verificação em duas etapas não pôde ser lida e foi reiniciada. Ative novamente.');
            }
        }

        return view('security', ['user' => $user]);
    }
}