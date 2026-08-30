<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Obriga usuários com perfil profissional (Fonoaudiólogo, Coordenador
 * Clínico, Administrador de TI) a terem o 2FA ativado antes de acessar
 * qualquer área do sistema que não seja a própria ativação do 2FA ou o
 * logout — reforça o requisito 1.5/1.6 para perfis que lidam com dado
 * clínico sensível (ver docs/requisito-1-autenticacao.md).
 */
class EnsureTwoFactorIsEnabled
{
    /**
     * Perfis que são obrigados a ter 2FA confirmado.
     */
    private array $perfisObrigatorios = [
        'fonoaudiologo',
        'coordenador_clinico',
        'administrador_ti',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        $precisaDe2fa = $user
            && in_array($user->perfil, $this->perfisObrigatorios)
            && is_null($user->two_factor_confirmed_at);

        // Libera acesso à própria tela de segurança e ao logout,
        // senão o usuário nunca conseguiria ativar o 2FA.
        $rotaLiberada = $request->routeIs('security.index')
            || $request->routeIs('two-factor.*')
            || $request->routeIs('password.confirm*')
            || $request->routeIs('logout');

        if ($precisaDe2fa && ! $rotaLiberada) {
            return redirect()->route('security.index')
                ->with('status', 'Por segurança, ative a verificação em duas etapas para continuar.');
        }

        return $next($request);
    }
}