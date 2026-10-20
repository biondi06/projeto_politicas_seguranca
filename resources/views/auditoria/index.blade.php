{{--
    Painel de auditoria — Requisito 5.4 (análise de logs).
    Dados vêm do AuditoriaController@index. CSS: .internal-*, .audit-* do app.css.
--}}
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auditoria — Ecoa</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:opsz,wght@6..72,400;6..72,500;6..72,600&family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>
<body class="internal-body">

    <div class="internal-topbar">
        <div class="container internal-topbar-row">
            <a href="{{ route('home') }}">&larr; Voltar ao painel</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="internal-topbar-logout">Sair</button>
            </form>
        </div>
    </div>

    <div class="container">
        <div class="internal-panel wide">

            <h4 style="margin-bottom:4px;">Auditoria e logs</h4>
            <p style="color:var(--muted); font-size:13.5px; margin-bottom:24px;">
                Trilha de eventos de segurança com verificação de integridade por cadeia de hash.
                Visível apenas para Coordenador Clínico e Administrador de TI.
            </p>

            @if (session('status'))
                <div class="internal-alert {{ session('status_type', 'success') }}">{{ session('status') }}</div>
            @endif

            {{-- INDICADORES --}}
            <div class="audit-stats four">
                <div class="audit-stat-card">
                    <div class="audit-stat-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 5 6v5c0 4.5 3 7.5 7 10 4-2.5 7-5.5 7-10V6l-7-3Z"></path><path d="m9 12 2 2 4-4"></path></svg>
                    </div>
                    <div>
                        <div class="audit-stat-value">{{ $totalEventos }}</div>
                        <div class="audit-stat-label">Eventos na cadeia</div>
                    </div>
                </div>

                <div class="audit-stat-card">
                    <div class="audit-stat-icon danger">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 8v4"></path><path d="M12 15h.01"></path></svg>
                    </div>
                    <div>
                        <div class="audit-stat-value">{{ $totalFalhasLogin }}</div>
                        <div class="audit-stat-label">Falhas de login</div>
                    </div>
                </div>

                <div class="audit-stat-card">
                    <div class="audit-stat-icon danger">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="9" rx="2"></rect><path d="M8 11V8a4 4 0 0 1 8 0v3"></path></svg>
                    </div>
                    <div>
                        <div class="audit-stat-value">{{ $totalFalhas2fa }}</div>
                        <div class="audit-stat-label">Falhas de 2FA</div>
                    </div>
                </div>

                <div class="audit-stat-card">
                    <div class="audit-stat-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 11v5"></path><path d="M12 8h.01"></path></svg>
                    </div>
                    <div>
                        <div class="audit-stat-value">{{ $totalLgpd }}</div>
                        <div class="audit-stat-label">Eventos de LGPD</div>
                    </div>
                </div>
            </div>

            {{-- VERIFICAÇÃO DE INTEGRIDADE --}}
            <div class="audit-actions">
                <form method="POST" action="{{ route('auditoria.verificar') }}" style="margin:0;">
                    @csrf
                    <button type="submit" class="btn-ecoa" style="width:auto; padding:11px 22px;">Verificar integridade da cadeia de logs</button>
                </form>
                <span class="audit-hint">Recalcula o hash de cada registro e confere com o valor armazenado.</span>
            </div>

            {{-- ANÁLISE: FALHAS POR IP --}}
            <h6>Análise — tentativas de login malsucedidas por IP</h6>
            <table class="internal-table">
                <thead><tr><th>IP</th><th>Ocorrências</th></tr></thead>
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
                        <tr><td colspan="2" style="color:var(--muted);">Nenhuma falha registrada.</td></tr>
                    @endforelse
                </tbody>
            </table>

            {{-- EVENTOS --}}
            <h6>Eventos registrados</h6>

            <form method="GET" action="{{ route('auditoria.index') }}" class="audit-filters">
                <div>
                    <label for="filtro">Tipo de evento</label>
                    <select id="filtro" name="filtro">
                        <option value="">Todos</option>
                        <option value="login" {{ $filtro === 'login' ? 'selected' : '' }}>Login</option>
                        <option value="2fa" {{ $filtro === '2fa' ? 'selected' : '' }}>Verificação em duas etapas</option>
                        <option value="lgpd" {{ $filtro === 'lgpd' ? 'selected' : '' }}>LGPD</option>
                    </select>
                </div>
                <div>
                    <label for="q">E-mail ou IP</label>
                    <input id="q" type="text" name="q" value="{{ $busca }}" placeholder="Buscar...">
                </div>
                <button type="submit" class="btn-outline" style="width:auto; padding:9px 18px;">Filtrar</button>
                @if ($filtro || $busca !== '')
                    <a href="{{ route('auditoria.index') }}" class="audit-page-btn">Limpar</a>
                @endif
            </form>

            <div style="overflow-x:auto;">
                <table class="internal-table" style="margin-bottom:0;">
                    <thead><tr><th>Evento</th><th>E-mail</th><th>IP</th><th>Data</th><th>Hash</th></tr></thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr>
                                <td><span class="internal-badge {{ \App\Support\EventoAuditoria::classe($log->evento) }}">{{ \App\Support\EventoAuditoria::rotulo($log->evento) }}</span></td>
                                <td>{{ $log->email ?? '—' }}</td>
                                <td>{{ $log->ip }}</td>
                                <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                <td style="font-family:'IBM Plex Mono', monospace; font-size:11px; color:var(--muted);">{{ substr($log->hash_atual, 0, 12) }}...</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" style="color:var(--muted);">Nenhum evento encontrado para esse filtro.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINAÇÃO (feita à mão: o componente padrão do Laravel depende de Tailwind) --}}
            <div class="audit-pager">
                <span>{{ $logs->total() }} evento(s) · página {{ $logs->currentPage() }} de {{ $logs->lastPage() }}</span>
                <div style="display:flex; gap:8px;">
                    <a href="{{ $logs->previousPageUrl() ?? '#' }}" class="audit-page-btn {{ $logs->onFirstPage() ? 'disabled' : '' }}">&larr; Anterior</a>
                    <a href="{{ $logs->nextPageUrl() ?? '#' }}" class="audit-page-btn {{ $logs->hasMorePages() ? '' : 'disabled' }}">Próxima &rarr;</a>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
