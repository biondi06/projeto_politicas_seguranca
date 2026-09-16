{{--
    Painel de direitos do titular (Requisito 4 — LGPD).
    4.8: consulta aos dados | 4.9: exportação | 4.6: revogação de
    consentimento | 4.10: exclusão dos dados pessoais.
--}}
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Dados — Ecoa</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    <style>
        body { background:#f4f6f2; font-family: system-ui, sans-serif; }
        .topbar { background:#0d3634; padding:18px 0; }
        .topbar a { color:#f4f6f2; text-decoration:none; font-weight:700; }
        .panel { max-width: 680px; margin: 40px auto; background:#fff; border-radius: 14px; padding: 32px; box-shadow: 0 8px 24px rgba(0,0,0,0.06); }
        .btn-ecoa { background:#c6873a; border:none; color:#241505; font-weight:600; }
        .btn-ecoa:hover { background:#b3792f; color:#241505; }
        .badge-ok { background:#2f7a4f; }
        .badge-off { background:#b3452f; }
    </style>
</head>
<body>
    <div class="topbar"><div class="container"><a href="{{ route('home') }}">&larr; Ecoa</a></div></div>

    <div class="container">
        <div class="panel">
            <h4 class="mb-4">Meus dados (LGPD)</h4>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <h6 class="text-muted text-uppercase small">Dados cadastrais</h6>
            <table class="table table-sm mb-4">
                <tr><th>Nome</th><td>{{ $user->name }}</td></tr>
                <tr><th>E-mail</th><td>{{ $user->email }}</td></tr>
                <tr><th>Perfil</th><td>{{ ucwords(str_replace('_', ' ', $user->perfil)) }}</td></tr>
                <tr><th>Cadastrado em</th><td>{{ $user->created_at->format('d/m/Y H:i') }}</td></tr>
            </table>

            <h6 class="text-muted text-uppercase small">Histórico de consentimento</h6>
            <table class="table table-sm mb-4">
                <thead><tr><th>Finalidade</th><th>Versão</th><th>Aceito em</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach ($consentimentos as $c)
                        <tr>
                            <td>{{ $c->finalidade }}</td>
                            <td>{{ $c->versao_termo }}</td>
                            <td>{{ $c->aceito_em->format('d/m/Y H:i') }}</td>
                            <td>
                                @if ($c->revogado_em)
                                    <span class="badge badge-off">Revogado em {{ $c->revogado_em->format('d/m/Y') }}</span>
                                @else
                                    <span class="badge badge-ok">Ativo</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('lgpd.exportar') }}" class="btn btn-ecoa">Exportar meus dados</a>

                <form method="POST" action="{{ route('lgpd.revogar') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary">Revogar consentimento</button>
                </form>

                <form method="POST" action="{{ route('lgpd.excluir') }}" onsubmit="return confirm('Isso vai anonimizar e excluir permanentemente sua conta. Confirma?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">Excluir meus dados</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>