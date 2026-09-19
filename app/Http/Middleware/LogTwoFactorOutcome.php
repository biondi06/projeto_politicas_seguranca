<?php

namespace App\Http\Middleware;

use App\Models\AuditoriaLog;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Requisito 5.2 — registra tentativas de código 2FA incorreto.
 */
class LogTwoFactorOutcome
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->routeIs('two-factor.login.store') && $request->session()->has('errors')) {
            $userId = $request->session()->get('login.id');
            $email = $userId ? optional(User::find($userId))->email : null;

            AuditoriaLog::registrar('2fa_falha', [
                'user_id' => $userId,
                'email' => $email,
            ]);
        }

        return $response;
    }
}