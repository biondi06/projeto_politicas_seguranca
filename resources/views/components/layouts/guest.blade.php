{{--
    Layout compartilhado para as telas de autenticação (login, registro,
    recuperação de senha, 2FA). Mantém uma única fonte de estilo/HTML
    para todo o fluxo do requisito 1 (Autenticação e Gestão de Credenciais).
--}}
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Ecoa' }}</title>

    {{-- Bootstrap (última versão estável) via CDN, conforme stack do projeto --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #0d3634;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: system-ui, sans-serif;
        }
        .auth-card {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border-radius: 14px;
            padding: 40px 36px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.25);
        }
        .auth-logo {
            font-size: 22px;
            font-weight: 700;
            color: #0d3634;
            margin-bottom: 6px;
        }
        .auth-logo span { color: #c6873a; }
        .auth-subtitle {
            color: #5b6660;
            font-size: 14px;
            margin-bottom: 26px;
        }
        .btn-ecoa {
            background: #c6873a;
            border: none;
            color: #241505;
            font-weight: 600;
        }
        .btn-ecoa:hover { background: #b3792f; color: #241505; }
    </style>
</head>
<body>

    <div class="auth-card">
        <div class="auth-logo">Ecoa<span>.</span></div>
        <div class="auth-subtitle">{{ $subtitle ?? 'Acesso ao sistema' }}</div>

        {{-- Slot principal: cada tela (login, registro, etc.) injeta seu formulário aqui --}}
        {{ $slot }}
    </div>

    {{-- jQuery, conforme stack do projeto --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>