<?php

namespace App\Http\Controllers;

use App\Models\AuditoriaLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Requisito 5.4 — exemplo de análise de logs, acessível apenas por
 * Coordenador Clínico e Administrador de TI.
 */
class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(
            in_array($request->user()->perfil, ['coordenador_clinico', 'administrador_ti']),
            403
        );

        $logs = AuditoriaLog::orderByDesc('id')->limit(50)->get();

        // Exemplo de análise: tentativas de login malsucedidas por IP
        $falhasPorIp = AuditoriaLog::where('evento', 'login_falha')
            ->select('ip', DB::raw('count(*) as total'))
            ->groupBy('ip')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('auditoria.index', [
            'logs' => $logs,
            'falhasPorIp' => $falhasPorIp,
            'totalEventos' => AuditoriaLog::count(),
            'totalFalhas' => AuditoriaLog::where('evento', 'login_falha')->count(),
        ]);
    }

    public function verificarIntegridade(Request $request)
    {
        abort_unless(
            in_array($request->user()->perfil, ['coordenador_clinico', 'administrador_ti']),
            403
        );

        $resultado = AuditoriaLog::verificarIntegridade();

        $mensagem = $resultado['adulterado']
            ? "Cadeia de logs violada a partir do registro #{$resultado['adulterado']}!"
            : "Integridade confirmada: {$resultado['total']} registros verificados, nenhuma adulteração detectada.";

        return back()->with('status', $mensagem);
    }
}