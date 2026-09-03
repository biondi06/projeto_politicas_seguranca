{{--
    Painel de segurança da conta — requisitos 1.5 (2FA implementada) e
    1.6 (validação do 2FA após autenticação primária).

    Estados possíveis do 2FA para o usuário logado:
    1. Desativado            -> two_factor_secret é nulo
    2. Ativado, não confirmado -> two_factor_secret existe, mas
                                   two_factor_confirmed_at é nulo
    3. Ativado e confirmado    -> two_factor_confirmed_at preenchido

    Cada estado mostra uma ação diferente, todas usando as rotas nativas
    do Laravel Fortify (nenhuma lógica de 2FA foi escrita à mão aqui —
    apenas a interface que aciona os endpoints já testados do pacote).
--}}
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Segurança da conta — Ecoa</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background:#f4f6f2; font-family: system-ui, sans-serif; }
        .topbar { background:#0d3634; padding:18px 0; }
        .topbar a { color:#f4f6f2; text-decoration:none; font-weight:700; }
        .panel {
            max-width: 560px; margin: 48px auto; background:#fff;
            border-radius: 14px; padding: 36px; box-shadow: 0 8px 24px rgba(0,0,0,0.06);
        }
        .btn-ecoa { background:#c6873a; border:none; color:#241505; font-weight:600; }
        .btn-ecoa:hover { background:#b3792f; color:#241505; }
        .badge-status { font-size: 13px; }
        #qr-code svg { margin: 16px auto; display:block; }
    </style>
</head>
<body>

    <div class="topbar">
        <div class="container">
            <a href="{{ route('home') }}" style="display:flex;align-items:center;gap:8px;"><img src="{{ asset('img/ecoa-icone.png') }}" alt="" style="height:22px;width:auto;">&larr; Ecoa</a>
        </div>
    </div>

    <div class="container">
        <div class="panel">
            <h4 class="mb-1">Verificação em duas etapas</h4>
            <p class="text-muted small mb-4">
                Protege sua conta exigindo um código adicional, gerado por um
                aplicativo autenticador (ex: Google Authenticator), além da senha.
            </p>

            {{-- ===================== ESTADO: DESATIVADO ===================== --}}
            @if (! $user->two_factor_secret)
                <span class="badge bg-secondary badge-status mb-3">Desativada</span>
                <p>Sua conta ainda não usa verificação em duas etapas.</p>

                {{-- Aciona Fortify\TwoFactorAuthenticationController@store,
                     que gera o segredo TOTP e os códigos de recuperação. --}}
                <form method="POST" action="{{ route('two-factor.enable') }}">
                    @csrf
                    <button type="submit" class="btn btn-ecoa">Ativar verificação em duas etapas</button>
                </form>

            {{-- ============== ESTADO: ATIVADA, AGUARDANDO CONFIRMAÇÃO ============== --}}
            @elseif (! $user->two_factor_confirmed_at)
                <span class="badge bg-warning text-dark badge-status mb-3">Aguardando confirmação</span>
                <p>Escaneie o QR code abaixo no seu aplicativo autenticador e informe o código gerado.</p>

                <div id="qr-code" class="text-center">Carregando QR code...</div>

                {{-- Confirma o código gerado pelo app, ativando de vez o 2FA
                     (Fortify\ConfirmedTwoFactorAuthenticationController@store) --}}
                <form method="POST" action="{{ route('two-factor.confirm') }}" class="mt-3">
                    @csrf
                    <div class="mb-3">
                        <label for="code" class="form-label">Código de 6 dígitos</label>
                        <input type="text" name="code" id="code" class="form-control" inputmode="numeric" autofocus required>
                    </div>
                    <button type="submit" class="btn btn-ecoa">Confirmar e ativar</button>
                </form>

                {{-- Busca o SVG do QR code via a rota nativa do Fortify e injeta na página --}}
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
                <script>
                    $.get("{{ route('two-factor.qr-code') }}", function (data) {
                        $('#qr-code').html(data.svg);
                    });
                </script>

            {{-- ===================== ESTADO: ATIVADA E CONFIRMADA ===================== --}}
            @else
                <span class="badge bg-success badge-status mb-3">Ativada</span>
                <p>Sua conta está protegida por verificação em duas etapas.</p>

                <button id="show-codes" class="btn btn-outline-secondary btn-sm mb-3">Ver códigos de recuperação</button>
                <ul id="recovery-codes" class="list-unstyled small font-monospace"></ul>

                {{-- Desativa o 2FA (Fortify\TwoFactorAuthenticationController@destroy) --}}
                <form method="POST" action="{{ route('two-factor.disable') }}" class="mt-3">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm">Desativar verificação em duas etapas</button>
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
