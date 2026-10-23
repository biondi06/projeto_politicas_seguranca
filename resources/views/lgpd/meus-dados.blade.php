{{--
    Meus dados e privacidade — Requisito 4 (LGPD).
    4.8 consulta | 4.9 exportação | 4.6 revogação | 4.10 exclusão | 4.4/4.7 consentimento.
    Dados vêm do LgpdController@meusDados. CSS: classes .lgpd-* do app.css.
--}}
@php
    // pendente = nenhum consentimento registrado; revogado = só há registros encerrados
    $estado = $consentimentoAtivo ? 'ativo' : ($consentimentos->isNotEmpty() ? 'revogado' : 'pendente');
    $perfilTexto = ucwords(str_replace('_', ' ', $user->perfil ?? 'Não definido'));
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus dados e privacidade — Ecoa</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:opsz,wght@6..72,400;6..72,500;6..72,600&family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>
<body class="internal-body">

    <div class="internal-topbar">
        <div class="container internal-topbar-row">
            @if ($estado === 'ativo')
                <a href="{{ route('home') }}">&larr; Voltar ao painel</a>
            @else
                <span class="internal-brand">Ecoa</span>
            @endif

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="internal-topbar-logout">Sair</button>
            </form>
        </div>
    </div>

    <main class="container lgpd-page">

        <div class="lgpd-header">
            <span class="lgpd-law">LGPD · Lei nº 13.709/2018</span>
            <h1>Meus dados e privacidade</h1>
            <p>Consulte, exporte e controle os dados pessoais que o Ecoa mantém sobre você. Todas as ações abaixo ficam registradas na trilha de auditoria.</p>
        </div>

        @if (session('status'))
            <div class="internal-alert success">{{ session('status') }}</div>
        @endif

        {{-- ===================== CONSENTIMENTO + DADOS CADASTRAIS ===================== --}}
        <div class="lgpd-grid">

            <section class="lgpd-card">
                <h2>Consentimento</h2>
                <p class="lgpd-lead">Autorização para o tratamento dos seus dados nas finalidades abaixo.</p>

                <div class="lgpd-status {{ $estado }}">
                    <span class="lgpd-status-dot"></span>
                    <div>
                        @if ($estado === 'ativo')
                            <strong>Consentimento ativo</strong>
                            <span class="lgpd-status-text">Você autorizou o tratamento dos seus dados para a finalidade descrita abaixo.</span>
                        @elseif ($estado === 'revogado')
                            <strong>Consentimento revogado</strong>
                            <span class="lgpd-status-text">O acesso às áreas do sistema está bloqueado. Seus dados não foram apagados: para isso, use a opção de exclusão.</span>
                        @else
                            <strong>Nenhum consentimento registrado</strong>
                            <span class="lgpd-status-text">Sua conta não tem um consentimento registrado. Conceda-o para usar o sistema.</span>
                        @endif
                    </div>
                </div>

                @if ($consentimentoAtivo)
                    <dl class="lgpd-dl">
                        <div style="grid-column: 1 / -1;">
                            <dt>Finalidade</dt>
                            <dd>{{ $consentimentoAtivo->finalidade }}</dd>
                        </div>
                        <div>
                            <dt>Versão do termo</dt>
                            <dd>{{ $consentimentoAtivo->versao_termo }}</dd>
                        </div>
                        <div>
                            <dt>Concedido em</dt>
                            <dd>{{ \App\Support\FusoHorario::formatar($consentimentoAtivo->aceito_em) }}</dd>
                        </div>
                    </dl>

                    <button type="button" class="lgpd-btn danger-outline" data-open="revogarDialog">Revogar consentimento</button>
                @else
                    <p class="lgpd-note" style="margin-top:0; margin-bottom:16px;">
                        Ao conceder, você declara ter lido os Termos de Uso e a Política de Privacidade do Ecoa (versão {{ $versaoTermo }}),
                        apresentados no cadastro, e autoriza o tratamento dos seus dados para o uso da plataforma e o acompanhamento terapêutico.
                    </p>
                    <form method="POST" action="{{ route('lgpd.consentir') }}">
                        @csrf
                        <button type="submit" class="lgpd-btn amber">Conceder consentimento (versão {{ $versaoTermo }})</button>
                    </form>
                @endif
            </section>

            <section class="lgpd-card">
                <h2>Dados da conta</h2>
                <p class="lgpd-lead">Informações cadastrais associadas ao seu acesso.</p>

                <dl class="lgpd-dl single" style="margin-bottom:0;">
                    <div>
                        <dt>Nome</dt>
                        <dd>{{ $user->name }}</dd>
                    </div>
                    <div>
                        <dt>E-mail</dt>
                        <dd>{{ $user->email }}</dd>
                    </div>
                    <div>
                        <dt>Perfil de acesso</dt>
                        <dd>{{ $perfilTexto }}</dd>
                    </div>
                    <div>
                        <dt>Cadastro</dt>
                        <dd>{{ \App\Support\FusoHorario::formatar($user->created_at) }}</dd>
                    </div>
                    <div>
                        <dt>Verificação em duas etapas</dt>
                        <dd>
                            @if ($user->two_factor_confirmed_at)
                                <span class="internal-badge success">Ativa</span>
                            @else
                                <span class="internal-badge warning">Inativa</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </section>
        </div>

        {{-- ===================== DIREITOS DO TITULAR ===================== --}}
        <section class="lgpd-card">
            <h2>Seus direitos</h2>
            <p class="lgpd-lead">O que você pode fazer com os seus dados, diretamente por aqui.</p>

            <div class="lgpd-right">
                <div>
                    <h3>Exportar meus dados</h3>
                    <p>Baixe uma cópia dos seus dados em um arquivo de texto (.txt): dados da conta e histórico de consentimentos. Senhas e segredos de autenticação nunca são incluídos.</p>
                </div>
                <a href="{{ route('lgpd.exportar') }}" class="lgpd-btn secondary">Exportar (.txt)</a>
            </div>

            <div class="lgpd-right danger">
                <div>
                    <h3>Excluir meus dados</h3>
                    <p>Anonimiza seus dados pessoais e encerra a sua conta. A ação é irreversível. Registros de segurança podem ser mantidos pelo mínimo necessário para auditoria.</p>
                </div>
                <button type="button" class="lgpd-btn danger-outline" data-open="excluirDialog">Excluir meus dados</button>
            </div>

            <p class="lgpd-note">
                Para outros direitos previstos no art. 18 da LGPD, como correção de dados ou informação sobre compartilhamento,
                escreva para <strong>fonoaudiologiaecoa@gmail.com</strong>.
            </p>
        </section>

        {{-- ===================== HISTÓRICO DE CONSENTIMENTOS ===================== --}}
        <section class="lgpd-card">
            <h2>Histórico de consentimentos</h2>
            <p class="lgpd-lead">Cada concessão e revogação fica registrada com data e versão do termo.</p>

            <div style="overflow-x:auto;">
                <table class="internal-table" style="margin-bottom:0;">
                    <thead>
                        <tr><th>Finalidade</th><th>Versão</th><th>Concedido em</th><th>Revogado em</th><th>Situação</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($consentimentos as $c)
                            <tr>
                                <td>{{ $c->finalidade }}</td>
                                <td>{{ $c->versao_termo }}</td>
                                <td>{{ \App\Support\FusoHorario::formatar($c->aceito_em) }}</td>
                                <td>{{ \App\Support\FusoHorario::formatar($c->revogado_em) }}</td>
                                <td>
                                    @if ($c->revogado_em)
                                        <span class="internal-badge danger">Revogado</span>
                                    @else
                                        <span class="internal-badge success">Ativo</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" style="color:var(--muted);">Nenhum consentimento registrado.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

    </main>

    {{-- ===================== JANELA: REVOGAR ===================== --}}
    <dialog class="lgpd-dialog" id="revogarDialog" aria-labelledby="revogarTitulo">
        <h3 id="revogarTitulo">Revogar consentimento?</h3>
        <p>Ao revogar:</p>
        <ul>
            <li>o acesso às áreas do sistema fica bloqueado até você conceder um novo consentimento;</li>
            <li>seus dados <strong>não</strong> são apagados (para isso, use “Excluir meus dados”);</li>
            <li>o histórico de consentimentos é preservado.</li>
        </ul>
        <div class="lgpd-dialog-actions">
            <button type="button" class="lgpd-btn secondary" data-close>Cancelar</button>
            <form method="POST" action="{{ route('lgpd.revogar') }}">
                @csrf
                <button type="submit" class="lgpd-btn danger">Revogar consentimento</button>
            </form>
        </div>
    </dialog>

    {{-- ===================== JANELA: EXCLUIR ===================== --}}
    <dialog class="lgpd-dialog" id="excluirDialog" aria-labelledby="excluirTitulo">
        <h3 id="excluirTitulo">Excluir meus dados?</h3>
        <p>Esta ação é <strong>irreversível</strong>. Seus dados pessoais serão anonimizados, a sua conta será encerrada e você será desconectado.</p>

        <form method="POST" action="{{ route('lgpd.excluir') }}">
            @csrf
            @method('DELETE')

            <label for="senha-exclusao">Digite a sua senha para confirmar</label>
            <input id="senha-exclusao" type="password" name="password" autocomplete="current-password" required>
            @error('password')
                <div class="lgpd-field-error">{{ $message }}</div>
            @enderror

            <div class="lgpd-dialog-actions">
                <button type="button" class="lgpd-btn secondary" data-close>Cancelar</button>
                <button type="submit" class="lgpd-btn danger">Excluir definitivamente</button>
            </div>
        </form>
    </dialog>

    <script>
        document.querySelectorAll('[data-open]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.getElementById(btn.dataset.open).showModal();
            });
        });

        document.querySelectorAll('[data-close]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                btn.closest('dialog').close();
            });
        });

        // clique no fundo escuro fecha a janela
        document.querySelectorAll('dialog.lgpd-dialog').forEach(function (d) {
            d.addEventListener('click', function (e) {
                if (e.target === d) d.close();
            });
        });

        // se a senha da exclusão estava errada, reabre a janela com o erro
        @if ($errors->has('password'))
            document.getElementById('excluirDialog').showModal();
        @endif
    </script>

</body>
</html>
