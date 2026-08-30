<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SecurityController extends Controller
{
    /**
     * Exibe o painel de segurança da conta, onde o usuário ativa,
     * confirma ou desativa a verificação em duas etapas (2FA) —
     * requisitos 1.5 e 1.6 do checklist de segurança.
     */

    // Inicia o processo de ativação do 2FA.
    // O Fortify gera o segredo TOTP e os códigos de recuperação,
    // que são protegidos antes de serem armazenados.
    public function index(Request $request)
    {
        return view('security', [
            'user' => $request->user(),
        ]);
    }
}