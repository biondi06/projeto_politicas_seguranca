{{--
    Painel de auditoria (Requisito 5.4 — exemplo de análise de logs).
    CSS vem do app.css global (classes .internal-*, .audit-*).
--}}
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auditoria — Ecoa</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    @vite(['resources/css/app.css'])
</head>

<body class="internal-body">

    <div class="internal-topbar">
        <div class="container"><a href="{{ route('home') }}">&larr; Ecoa</a></div>
    </div>

    <div class="container">
        <div class="internal-panel wide">

            <h4 style="margin-bottom: 4px;">Auditoria e Logs</h4>
            <p style="color: var(--muted); font-size: 13.5px; margin-bottom: 24px;">
                Trilha de eventos de segurança com verificação de integridade por cadeia de hash.
                Visível apenas para Coordenador Clínico e Administrador de TI.
            </p>

            @if (session('status'))
            <div class="internal-alert {{ session('status_type', 'success') }}">
                {{ session('status') }}
            </div>
            @endif

            {{-- CARDS DE ESTATÍSTICA --}}
            <div class="audit-stats">
                <div class="audit-stat-card">
                    <div class="audit-stat-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 3 5 6v5c0 4.5 3 7.5 7 10 4-2.5 7-5.5 7-10V6l-7-3Z"></path>
                            <path d="m9 12 2 2 4-4"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="audit-stat-value">{{ $totalEventos }}</div>
                        <div class="audit-stat-label">Eventos registrados na cadeia</div>
                    </div>
                </div>

                <div class="audit-stat-card">
                    <div class="audit-stat-icon danger">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="M12 8v4"></path>
                            <path d="M12 15h.01"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="audit-stat-value">{{ $totalFalhas }}</div>
                        <div class="audit-stat-label">Tentativas de login malsucedidas</div>
                    </div>
                </div>
            </div>

            {{-- AÇÃO DE VERIFICAÇÃO --}}
            <div class="audit-actions">
                <form method="POST" action="{{ route('auditoria.verificar') }}" style="margin:0;">
                    @csrf
                    <button type="submit" class="btn-ecoa" style="width:auto; padding:11px 22px;">
                        Verificar integridade da cadeia de logs
                    </button>
                </form>
                <span class="audit-hint">Recalcula o hash de cada registro e confere com o valor armazenado</span>
            </div>

            {{-- ANÁLISE: FALHAS POR IP --}}
            <h6>Exemplo de análise — tentativas de login malsucedidas por IP</h6>
            <table class="internal-table">
                <thead>
                    <tr>
                        <th>IP</th>
                        <th>Ocorrências</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($falhasPorIp as $item)
                    <tr>
                        <td>{{ $item->ip }}</td>
                        <td>
                            {{ $item->total }}
                            @if ($item->total >= 5)
                            <span class="internal-badge danger" style="margin-left:8px;">possível força bruta</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" style="color:var(--muted);">Nenhuma falha registrada</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- ÚLTIMOS EVENTOS --}}
            <h6>Últimos 50 eventos</h6>
            <div style="max-height:400px; overflow:auto;">
                <table class="internal-table">
                    <thead>
                        <tr>
                            <th>Evento</th>
                            <th>E-mail</th>
                            <th>IP</th>
                            <th>Data</th>
                            <th>Hash</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $log)
                        @php
                        $badge = match (true) {
                        str_contains($log->evento, 'sucesso') => 'success',
                        str_contains($log->evento, 'falha') => 'danger',
                        default => 'neutral',
                        };
                        @endphp
                        <tr>
                            <td><span class="internal-badge {{ $badge }}">{{ $log->evento }}</span></td>
                            <td>{{ $log->email ?? '—' }}</td>
                            <td>{{ $log->ip }}</td>
                            <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            <td style="font-family:'IBM Plex Mono', monospace; font-size:11px; color:var(--muted);">
                                {{ substr($log->hash_atual, 0, 12) }}...
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</body>

</html>