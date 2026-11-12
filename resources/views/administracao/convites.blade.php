{{--
    Acessos profissionais — provisionamento de perfis por convite (Administrador de TI).

    É por aqui que contas de fonoaudiólogo, coordenador clínico e administrador
    passam a existir: o administrador informa um e-mail e um perfil, o Ecoa envia
    o convite por e-mail, e a pessoa cria a própria senha.
    Dados vêm do ConviteAcessoController@index. CSS: .internal-*, .admin-*, .invite-*
    e .table-cards do app.css (no celular, a tabela vira cartões).
--}}
@php
    // Contagem por situação, para os indicadores acima da lista.
    $contagem = ['pendente' => 0, 'usado' => 0, 'revogado' => 0, 'expirado' => 0];
    foreach ($convites as $item) {
        $contagem[$item->situacao()]++;
    }
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acessos profissionais — Ecoa</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:opsz,wght@6..72,400;6..72,500;6..72,600&family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>
<body class="internal-body">

    <div class="internal-topbar">
        <div class="container internal-topbar-row">
            <nav class="internal-topbar-nav" aria-label="Administração">
                <a href="{{ route('home') }}">&larr; Painel</a>
                @can('gerenciar-acessos')
                    <a href="{{ route('convites.index') }}" class="active">Acessos<span class="so-desktop"> profissionais</span></a>
                @endcan
                @can('ver-auditoria')
                    <a href="{{ route('auditoria.index') }}">Auditoria<span class="so-desktop"> e logs</span></a>
                @endcan
            </nav>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="internal-topbar-logout">Sair</button>
            </form>
        </div>
    </div>

    <div class="container">
        <div class="internal-panel wide">

            <div class="admin-head">
                <div>
                    <h4>Acessos profissionais</h4>
                    <p>
                        Contas de fonoaudiólogo, coordenador clínico e administrador só são criadas por convite.
                        Você informa o e-mail e o perfil, o Ecoa envia o convite à pessoa, e ela cria a própria senha.
                        Você nunca vê nem define a senha de ninguém.
                    </p>
                </div>
            </div>

            @if (session('status'))
                <div class="internal-alert {{ session('status_type', 'success') }}">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="internal-alert danger">
                    @foreach ($errors->all() as $erro)
                        {{ $erro }}
                    @endforeach
                </div>
            @endif

            {{-- LINK DO CONVITE RECÉM-CRIADO (aparece uma única vez) --}}
            @if (session('convite_link'))
                <div class="invite-link">
                    <strong>Link do convite para {{ session('convite_email') }}</strong>
                    <div class="invite-link-row">
                        <input id="linkConvite" type="text" readonly value="{{ session('convite_link') }}" aria-label="Link do convite">
                        <button type="button" id="copiarLink" class="lgpd-btn secondary">Copiar link</button>
                    </div>
                    <p>
                        Este link aparece só agora e dá acesso ao perfil escolhido. Envie por um canal que só a pessoa convidada acessa.
                        Quando o envio de e-mail estiver configurado, o convite segue direto para o e-mail da pessoa e este link deixa de aparecer.
                        Para testar, abra-o numa janela anônima, pois quem já está logado é redirecionado.
                    </p>
                </div>
            @endif

            {{-- NOVO CONVITE --}}
            <div class="admin-card">
                <h6>Novo convite</h6>
                <form method="POST" action="{{ route('convites.store') }}" class="invite-form">
                    @csrf
                    <div>
                        <label for="email">E-mail da pessoa</label>
                        <input id="email" type="text" inputmode="email" name="email" value="{{ old('email') }}"
                               autocomplete="off" placeholder="nome@exemplo.com" required>
                    </div>
                    <div>
                        <label for="perfil">Perfil de acesso</label>
                        <select id="perfil" name="perfil" required>
                            <option value="" disabled selected>Selecione...</option>
                            @foreach ($perfis as $perfil)
                                <option value="{{ $perfil->value }}" {{ old('perfil') === $perfil->value ? 'selected' : '' }}>{{ $perfil->rotulo() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn-ecoa">Enviar convite</button>
                </form>
            </div>

            {{-- CONVITES EMITIDOS --}}
            <div class="admin-section">
                <h6>Convites emitidos</h6>

                <div class="admin-chips">
                    <span class="admin-chip"><strong>{{ $contagem['pendente'] }}</strong> pendente(s)</span>
                    <span class="admin-chip"><strong>{{ $contagem['usado'] }}</strong> usado(s)</span>
                    <span class="admin-chip"><strong>{{ $contagem['revogado'] + $contagem['expirado'] }}</strong> revogado(s) ou expirado(s)</span>
                </div>

                <table class="internal-table table-cards" style="margin-bottom:0;">
                    <thead>
                        <tr><th>E-mail</th><th>Perfil</th><th>Situação</th><th>Criado por</th><th>Validade</th><th></th></tr>
                    </thead>
                    <tbody>
                        @forelse ($convites as $convite)
                            @php
                                $situacao = $convite->situacao();
                                $classeSelo = ['pendente' => 'warning', 'usado' => 'success', 'revogado' => 'danger', 'expirado' => 'neutral'][$situacao];
                            @endphp
                            <tr>
                                <td class="cell-title" data-label="E-mail">{{ $convite->email }}</td>
                                <td data-label="Perfil">{{ $convite->perfil->rotulo() }}</td>
                                <td data-label="Situação"><span class="internal-badge {{ $classeSelo }}">{{ ucfirst($situacao) }}</span></td>
                                <td data-label="Criado por">{{ $convite->criador?->name ?? '—' }}</td>
                                <td data-label="Validade">
                                    @if ($situacao === 'usado')
                                        usado em {{ \App\Support\FusoHorario::formatar($convite->usado_em) }}
                                    @else
                                        até {{ \App\Support\FusoHorario::formatar($convite->expira_em) }}
                                    @endif
                                </td>
                                <td class="cell-actions">
                                    @if ($situacao === 'pendente')
                                        <div class="convite-acoes">
                                            <form method="POST" action="{{ route('convites.reenviar', $convite) }}">
                                                @csrf
                                                <button type="submit" class="lgpd-btn secondary">Reenviar</button>
                                            </form>
                                            <form method="POST" action="{{ route('convites.destroy', $convite) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="lgpd-btn danger-outline">Revogar</button>
                                            </form>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="cell-empty">Nenhum convite emitido ainda.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <script>
        var botaoCopiar = document.getElementById('copiarLink');

        if (botaoCopiar) {
            botaoCopiar.addEventListener('click', function () {
                var campo = document.getElementById('linkConvite');

                campo.select();

                // Copia pelo recurso moderno; se o navegador recusar, o texto já
                // está selecionado e a pessoa pode copiar com Ctrl+C.
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(campo.value).then(function () {
                        botaoCopiar.textContent = 'Copiado';
                        setTimeout(function () { botaoCopiar.textContent = 'Copiar link'; }, 2500);
                    });
                }
            });
        }
    </script>

</body>
</html>
