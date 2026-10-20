<?php

namespace App\Http\Controllers;

use App\Models\AuditoriaLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Requisito 5.4 — análise dos logs de auditoria. Acesso restrito a
 * Coordenador Clínico e Administrador de TI (regra RN-18).
 */
class AuditoriaController extends Controller
{
    /** Filtro da tela => padrão LIKE aplicado ao código do evento. */
    private const FILTROS = [
        'login' => 'login_%',
        '2fa'   => '2fa_%',
        'lgpd'  => 'lgpd_%',
    ];

    private function autorizar(Request $request): void
    {
        abort_unless(
            in_array($request->user()->perfil, ['coordenador_clinico', 'administrador_ti'], true),
            403
        );
    }

    public function index(Request $request)
    {
        $this->autorizar($request);

        $filtro = $request->query('filtro');
        $busca = trim((string) $request->query('q', ''));

        $consulta = AuditoriaLog::query()->orderByDesc('id');

        if (isset(self::FILTROS[$filtro])) {
            $consulta->where('evento', 'like', self::FILTROS[$filtro]);
        } else {
            $filtro = null;
        }

        if ($busca !== '') {
            $consulta->where(function ($q) use ($busca) {
                $q->where('email', 'like', '%' . $busca . '%')
                  ->orWhere('ip', 'like', '%' . $busca . '%');
            });
        }

        // Falhas de login agrupadas por IP (exemplo de análise, requisito 5.4)
        $falhasPorIp = AuditoriaLog::where('evento', 'login_falha')
            ->select('ip', DB::raw('count(*) as total'))
            ->groupBy('ip')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('auditoria.index', [
            'logs' => $consulta->paginate(20)->withQueryString(),
            'filtro' => $filtro,
            'busca' => $busca,
            'falhasPorIp' => $falhasPorIp,
            'totalEventos' => AuditoriaLog::count(),
            'totalFalhasLogin' => AuditoriaLog::where('evento', 'login_falha')->count(),
            'totalFalhas2fa' => AuditoriaLog::where('evento', '2fa_falha')->count(),
            'totalLgpd' => AuditoriaLog::where('evento', 'like', 'lgpd_%')->count(),
        ]);
    }

    public function verificarIntegridade(Request $request)
    {
        $this->autorizar($request);

        $resultado = AuditoriaLog::verificarIntegridade();
        $adulterado = (bool) $resultado['adulterado'];

        $mensagem = $adulterado
            ? "Cadeia de logs violada a partir do registro #{$resultado['adulterado']}. A integridade dos registros não pôde ser confirmada."
            : "Integridade confirmada: {$resultado['total']} registros verificados, nenhuma adulteração detectada.";

        return redirect()->route('auditoria.index')->with([
            'status' => $mensagem,
            'status_type' => $adulterado ? 'danger' : 'success',
        ]);
    }
}
