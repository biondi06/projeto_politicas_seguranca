{{--
    Corrigir meus dados — Requisito 4 (LGPD), art. 18, III: correção de dados
    incompletos, inexatos ou desatualizados.
    Dados vêm do CorrecaoDadosController@edit. CSS: .lgpd-* do app.css.
--}}
@php
    $perfilTexto = ucwords(str_replace('_', ' ', $user->perfil ?? 'Não definido'));
    $temErroSenha = $errors->has('senha_atual');
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corrigir meus dados — Ecoa</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:opsz,wght@6..72,400;6..72,500;6..72,600&family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>
<body class="internal-body">

    <div class="internal-topbar">
        <div class="container internal-topbar-row">
            <nav class="internal-topbar-nav" aria-label="Navegação">
                <a href="{{ route('lgpd.meus-dados') }}">&larr; Meus dados</a>
            </nav>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="internal-topbar-logout">Sair</button>
            </form>
        </div>
    </div>

    <main class="container lgpd-page">

        <div class="lgpd-header">
            <span class="lgpd-law">LGPD · art. 18, III</span>
            <h1>Corrigir meus dados</h1>
            <p>Se o seu nome ou o seu e-mail estiverem incorretos ou desatualizados, corrija aqui mesmo. Você não precisa pedir a ninguém.</p>
        </div>

        @if (session('status'))
            <div class="internal-alert {{ session('status_type', 'success') }}">{{ session('status') }}</div>
        @endif

        <div class="lgpd-grid">

            <section class="lgpd-card">
                <h2>Dados da conta</h2>
                <p class="lgpd-lead">Altere apenas o que estiver errado.</p>

                <form method="POST" action="{{ route('lgpd.corrigir.salvar') }}" class="lgpd-form">
                    @csrf
                    @method('PATCH')

                    <div class="lgpd-field">
                        <label for="name">Nome completo</label>
                        <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}"
                               required minlength="2" maxlength="255" autocomplete="name"
                               @error('name') aria-invalid="true" aria-describedby="name-erro" @enderror>
                        @error('name')
                            <div class="lgpd-field-error" id="name-erro">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="lgpd-field">
                        <label for="email">E-mail</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}"
                               data-original="{{ $user->email }}" required maxlength="255" autocomplete="email"
                               @error('email') aria-invalid="true" aria-describedby="email-erro" @enderror>
                        <div class="lgpd-field-help">É com ele que você entra no Ecoa e recebe a recuperação de senha.</div>
                        @error('email')
                            <div class="lgpd-field-error" id="email-erro">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Só é necessária ao trocar o e-mail; o script abaixo a esconde enquanto o e-mail não muda.
                         Sem JavaScript ela fica sempre visível, e o servidor continua exigindo quando preciso. --}}
                    <div class="lgpd-field" id="campoSenha">
                        <label for="senha_atual">Senha atual</label>
                        <input id="senha_atual" type="password" name="senha_atual" autocomplete="current-password"
                               @error('senha_atual') aria-invalid="true" aria-describedby="senha-erro" @enderror>
                        <div class="lgpd-field-help">Necessária só para alterar o e-mail. Protege a sua conta caso alguém use o seu aparelho.</div>
                        @error('senha_atual')
                            <div class="lgpd-field-error" id="senha-erro">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="lgpd-field">
                        <span class="lgpd-field-label">Perfil de acesso</span>
                        <div class="lgpd-readonly">{{ $perfilTexto }}</div>
                        <div class="lgpd-field-help">Definido pela administração do Ecoa e não pode ser alterado aqui.</div>
                    </div>

                    <div class="lgpd-form-actions">
                        <button type="submit" class="lgpd-btn amber">Salvar correções</button>
                        <a href="{{ route('lgpd.meus-dados') }}" class="lgpd-btn secondary">Cancelar</a>
                    </div>
                </form>
            </section>

            <section class="lgpd-card">
                <h2>Como isso funciona</h2>
                <p class="lgpd-lead">O que acontece com a sua correção.</p>

                <ul class="lgpd-points">
                    <li><strong>É um direito seu.</strong> A LGPD (art. 18, III) garante a correção de dados incompletos, inexatos ou desatualizados.</li>
                    <li><strong>Fica registrado.</strong> A correção entra na trilha de auditoria. Guardamos quais campos mudaram, e não os valores.</li>
                    <li><strong>Mais segurança.</strong> Se você trocar o e-mail, o endereço antigo recebe um aviso, para o caso de a alteração não ter sido feita por você.</li>
                    <li><strong>Outros dados.</strong> Para corrigir dados de uma criança acompanhada, ou algo que não aparece aqui, escreva para <strong>fonoaudiologiaecoa@gmail.com</strong>.</li>
                </ul>
            </section>

        </div>
    </main>

    <script>
        (function () {
            var email = document.getElementById('email');
            var campo = document.getElementById('campoSenha');
            var senha = document.getElementById('senha_atual');
            var original = (email.dataset.original || '').trim().toLowerCase();
            var comErro = {{ $temErroSenha ? 'true' : 'false' }};

            function sincronizar() {
                var mudou = email.value.trim().toLowerCase() !== original;

                campo.hidden = !mudou && !comErro;
                senha.required = mudou;
            }

            email.addEventListener('input', sincronizar);
            sincronizar();
        })();
    </script>

</body>
</html>
