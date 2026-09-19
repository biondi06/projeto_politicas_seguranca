<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Auditoria — Ecoa</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    <style>
        body { background:#f4f6f2; font-family: system-ui, sans-serif; }
        .topbar { background:#0d3634; padding:18px 0; }
        .topbar a { color:#f4f6f2; text-decoration:none; font-weight:700; }
        .panel { max-width: 960px; margin: 40px auto; background:#fff; border-radius: 14px; padding: 32px; box-shadow: 0 8px 24px rgba(0,0,0,0.06); }
        .btn-ecoa { background:#c6873a; border:none; color:#241505; font-weight:600; }
        .value { font-size: 32px; font-weight: 700; color:#1b5e5a; }
        code { font-size: 11px; }
    </style>
</head>
<body>
    <div class="topbar"><div class="container"><a href="{{ route('home') }}">&larr; Ecoa</a></div></div>

    <div class="container">
        <div class="panel">
            <h4 class="mb-4">Auditoria e Logs</h4>

            @if (session('status'))
                <div class="alert alert-info">{{ session('status') }}</div>
            @endif

            <div class="row mb-4">
                <div class="col"><div class="value">{{ $totalEventos }}</div><small class="text-muted">Eventos registrados</small></div>
                <div class="col"><div class="value">{{ $totalFalhas }}</div><small class="text-muted">Falhas de login</small></div>
            </div>

            <form method="POST" action="{{ route('auditoria.verificar') }}" class="mb-4">
                @csrf
                <button type="submit" class="btn btn-ecoa">Verificar integridade da cadeia de logs</button>
            </form>

            <h6 class="text-muted text-uppercase small">Exemplo de análise — tentativas de login malsucedidas por IP</h6>
            <table class="table table-sm mb-4">
                <thead><tr><th>IP</th><th>Ocorrências</th></tr></thead>
                <tbody>
                    @forelse ($falhasPorIp as $item)
                        <tr><td>{{ $item->ip }}</td><td>{{ $item->total }}</td></tr>
                    @empty
                        <tr><td colspan="2" class="text-muted">Nenhuma falha registrada</td></tr>
                    @endforelse
                </tbody>
            </table>

            <h6 class="text-muted text-uppercase small">Últimos 50 eventos</h6>
            <div style="max-height:400px; overflow:auto;">
                <table class="table table-sm">
                    <thead><tr><th>Evento</th><th>E-mail</th><th>IP</th><th>Data</th><th>Hash</th></tr></thead>
                    <tbody>
                        @foreach ($logs as $log)
                            <tr>
                                <td>{{ $log->evento }}</td>
                                <td>{{ $log->email }}</td>
                                <td>{{ $log->ip }}</td>
                                <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                <td><code>{{ substr($log->hash_atual, 0, 12) }}...</code></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>