<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Requisitos 3.1/3.2 — garante que toda comunicação em produção
 * ocorra via HTTPS, bloqueando/redirecionando conexões inseguras.
 */
class EnsureHttps
{
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('production') && ! $request->secure()) {
            return redirect()->secure($request->getRequestUri());
        }

        return $next($request);
    }
}