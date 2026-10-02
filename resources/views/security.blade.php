{{--
    Painel de segurança da conta — requisitos 1.5 (2FA implementada) e
    1.6 (validação do 2FA após autenticação primária). CSS vem do
    app.css global (classes .internal-*).
--}}
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Segurança da conta — Ecoa</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    @vite(['resources/css/app.css'])
</head>
<body class="internal-body">

    <div class="internal-topbar">
        <div class="container">
            <a href="{{ route('home') }}">&larr; Ecoa</a>
        </div>
    </div>

    <div class="container">
        <div class="internal-panel">
            <h4>Verificação em duas etapas</h4>
            <p class="security-desc" style="margin-bottom:24px;">
                Protege sua conta exigindo um código adicional, gerado por um
                aplicativo autenticador (ex: Google Authenticator), além da senha.
            </p>

            {{-- ===================== ESTADO: DESATIVADO ===================== --}}
            @if (! $user->two_factor_secret)
                <span class="internal-badge neutral" style="margin-bottom:12px; display:inline-block;">Desativada</span>
                <p style="margin-bottom:18px;">Sua conta ainda não usa verificação em duas etapas.</p>

                <form method="POST" action="{{ route('two-factor.enable') }}">
                    @csrf
                    <button type="submit" class="btn-ecoa" style="width:auto; padding:11px 22px;">Ativar verificação em duas etapas</button>
                </form>

            {{-- ============== ESTADO: ATIVADA, AGUARDANDO CONFIRMAÇÃO ============== --}}
            @elseif (! $user->two_factor_confirmed_at)
                <span class="internal-badge warning" style="margin-bottom:12px; display:inline-block;">Aguardando confirmação</span>
                <p style="margin-bottom:18px;">Escaneie o QR code abaixo no seu aplicativo autenticador e informe o código gerado.</p>

                <div id="qr-code" style="text-align:center;">Carregando QR code...</div>

                <form method="POST" action="{{ route('two-factor.confirm') }}" style="margin-top:18px;">
                    @csrf
                    <div class="auth-form-group">
                        <label for="code">Código de 6 dígitos</label>
                        <input type="text" name="code" id="code" inputmode="numeric" autofocus required>
                    </div>
                    <button type="submit" class="btn-ecoa" style="width:auto; padding:11px 22px;">Confirmar e ativar</button>
                </form>

                <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
                <script>
                    $.get("{{ route('two-factor.qr-code') }}", function (data) {
                        $('#qr-code').html(data.svg);
                    });
                </script>

            {{-- ===================== ESTADO: ATIVADA E CONFIRMADA ===================== --}}
            @else
                <span class="internal-badge success" style="margin-bottom:12px; display:inline-block;">Ativada</span>
                <p style="margin-bottom:18px;">Sua conta está protegida por verificação em duas etapas.</p>

                <button id="show-codes" class="btn-outline" style="width:auto; padding:9px 16px; margin-bottom:14px;">Ver códigos de recuperação</button>
                <ul id="recovery-codes" style="list-style:none; font-family:'IBM Plex Mono', monospace; font-size:12.5px; margin-bottom:18px;"></ul>

                <form method="POST" action="{{ route('two-factor.disable') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-outline" style="width:auto; padding:9px 16px; border-color:var(--danger); color:var(--danger);">Desativar verificação em duas etapas</button>
                </form>

                <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
                <script>
                    $('#show-codes').on('click', function () {
                        $.get("{{ route('two-factor.recovery-codes') }}", function (codes) {
                            const list = codes.map(c => `<li>${c}</li>`).join('');
                            $('#recovery-codes').html(list);
                        });
                    });
                </script>
            @endif
        </div>
    </div>

</body>
</html>
