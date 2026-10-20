<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Requisito 4.6 — a revogação do consentimento precisa ter efeito.
 *
 * Sem consentimento ativo (revogado, ou nunca registrado), o usuário só
 * enxerga a página "Meus dados", onde pode consultar, exportar, excluir ou
 * conceder um novo consentimento. As rotas de direitos do titular ficam FORA
 * do grupo que usa este middleware (ver routes/web.php), senão haveria
 * redirecionamento infinito.
 */
class EnsureActiveConsent
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();

        if ($usuario && ! $usuario->consentimentos()->whereNull('revogado_em')->exists()) {
            return redirect()
                ->route('lgpd.meus-dados')
                ->with('status', 'Para continuar usando o Ecoa é necessário um consentimento ativo. Revise as informações abaixo e conceda o consentimento.');
        }

        return $next($request);
    }
}
