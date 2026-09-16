<?php

namespace App\Http\Controllers;

use App\Models\Consentimento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Atende ao Requisito 4 (Conformidade com a LGPD):
 * consulta, exportação, revogação e exclusão dos dados do titular.
 */
class LgpdController extends Controller
{
    // 4.8 — Funcionalidade de consulta aos dados do titular
    public function meusDados(Request $request)
    {
        $user = $request->user();

        $consentimentos = Consentimento::where('user_id', $user->id)
            ->orderByDesc('aceito_em')
            ->get();

        return view('lgpd.meus-dados', compact('user', 'consentimentos'));
    }

    // 4.9 — Funcionalidade de exportação dos dados
    public function exportar(Request $request)
    {
        $user = $request->user();

        $dados = [
            'usuario' => $user->only([
                'id',
                'name',
                'email',
                'perfil',
                'created_at',
            ]),

            'consentimentos' => Consentimento::where('user_id', $user->id)
                ->get()
                ->toArray(),
        ];

        $json = json_encode(
            $dados,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );

        return response($json, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="meus-dados-ecoa.json"',
        ]);
    }

    // 4.6 — Possibilidade de revogação do consentimento
    public function revogarConsentimento(Request $request)
    {
        Consentimento::where('user_id', $request->user()->id)
            ->whereNull('revogado_em')
            ->update([
                'revogado_em' => now(),
            ]);

        return back()->with(
            'status',
            'Consentimento revogado com sucesso.'
        );
    }

    // 4.10 — Funcionalidade de exclusão dos dados pessoais
    public function excluir(Request $request)
    {
        $user = $request->user();
        $id = $user->id;

        // Anonimiza os dados identificáveis antes da exclusão lógica.
        $user->update([
            'name' => 'Usuário removido',
            'email' => 'removido+' . $id . '@ecoa.invalid',
        ]);

        // Exclusão lógica através do SoftDeletes.
        $user->delete();

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with(
            'status',
            'Seus dados foram removidos com sucesso.'
        );
    }
}

