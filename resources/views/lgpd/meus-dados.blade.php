{{--
    Painel de direitos do titular (Requisito 4 — LGPD).
    4.8: consulta | 4.9: exportação | 4.6: revogação | 4.10: exclusão.
    CSS vem do app.css global (classes .internal-*).
--}}
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Dados — Ecoa</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    @vite(['resources/css/app.css'])
</head>
<body class="internal-body">

    <div class="internal-topbar">
        <div class="container"><a href="{{ route('home') }}">&larr; Ecoa</a></div>
    </div>

    <div class="container">
        <div class="internal-panel">
            <h4>Meus dados (LGPD)</h4>

            @if (session('status'))
                <div class="internal-alert">{{ session('status') }}</div>
            @endif

            <h6>Dados cadastrais</h6>
            <table class="internal-table">
                <tr><th>Nome</th><td>{{ $user->name }}</td></tr>
                <tr><th>E-mail</th><td>{{ $user->email }}</td></tr>
                <tr><th>Perfil</th><td>{{ ucwords(str_replace('_', ' ', $user->perfil)) }}</td></tr>
                <tr><th>Cadastrado em</th><td>{{ $user->created_at->format('d/m/Y H:i') }}</td></tr>
            </table>

            <h6>Histórico de consentimento</h6>
            <table class="internal-table">
                <thead><tr><th>Finalidade</th><th>Versão</th><th>Aceito em</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach ($consentimentos as $c)
                        <tr>
                            <td>{{ $c->finalidade }}</td>
                            <td>{{ $c->versao_termo }}</td>
                            <td>{{ $c->aceito_em->format('d/m/Y H:i') }}</td>
                            <td>
                                @if ($c->revogado_em)
                                    <span class="internal-badge danger">Revogado em {{ $c->revogado_em->format('d/m/Y') }}</span>
                                @else
                                    <span class="internal-badge success">Ativo</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <a href="{{ route('lgpd.exportar') }}" class="btn-ecoa" style="width:auto; padding:11px 20px; display:inline-flex;">Exportar meus dados</a>

                <form method="POST" action="{{ route('lgpd.revogar') }}">
                    @csrf
                    <button type="submit" class="btn-outline" style="width:auto; padding:10px 18px;">Revogar consentimento</button>
                </form>

                <form method="POST" action="{{ route('lgpd.excluir') }}" onsubmit="return confirm('Isso vai anonimizar e excluir permanentemente sua conta. Confirma?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-outline" style="width:auto; padding:10px 18px; border-color:var(--danger); color:var(--danger);">Excluir meus dados</button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
