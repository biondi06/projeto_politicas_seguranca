<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Requisito 2.7 (falha) — registra tentativas malsucedidas de
 * redefinição de senha (token inválido, expirado ou já utilizado).
 *
 * Em vez de interceptar a ValidationException diretamente (o Laravel
 * trata esse tipo de exceção de forma especial internamente, antes de
 * chegar aos handlers customizados), verificamos o resultado da própria
 * requisição: se a rota é "password.update" e a sessão recebeu erros de
 * validação, é porque o reset falhou.
 */
class LogPasswordResetOutcome
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->routeIs('password.update') && $request->session()->has('errors')) {
            Log::channel('single')->warning('Falha na redefinição de senha.', [
                'email' => $request->input('email'),
                'ip' => $request->ip(),
            ]);
        }

        return $response;
    }
}