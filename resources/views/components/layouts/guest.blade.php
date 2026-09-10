{{--
    Layout compartilhado para as telas de autenticação (login, registro,
    recuperação de senha, 2FA). Inclui um sistema de notificação (toast)
    que exibe mensagens de sucesso (session('status')) e de erro
    (validação do formulário) de forma consistente em todas as telas.
--}}
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Ecoa' }}</title>

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

        /* ===================== TOAST ===================== */
        #ecoa-toast-container {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 2000;
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-width: 360px;
        }

        .ecoa-toast {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 16px 18px;
            border-radius: 10px;
            box-shadow: 0 12px 28px rgba(0,0,0,0.18);
            font-size: 14px;
            line-height: 1.4;
            opacity: 0;
            transform: translateX(30px);
            transition: opacity .3s ease, transform .3s ease;
        }

        .ecoa-toast.show {
            opacity: 1;
            transform: translateX(0);
        }

        .ecoa-toast.success {
            background: #eaf4ee;
            border: 1px solid #b9d9c4;
            color: #1b5e3a;
        }

        .ecoa-toast.error {
            background: #fbeae6;
            border: 1px solid #e3b3a5;
            color: #8a2f1c;
        }

        .ecoa-toast .icon {
            flex-shrink: 0;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-weight: 700;
            font-size: 12px;
            color: #fff;
        }

        .ecoa-toast.success .icon { background: #2f7a4f; }
        .ecoa-toast.error .icon { background: #b3452f; }

        .ecoa-toast .close-btn {
            margin-left: auto;
            background: none;
            border: none;
            color: inherit;
            opacity: .6;
            cursor: pointer;
            font-size: 16px;
            line-height: 1;
        }
        .ecoa-toast .close-btn:hover { opacity: 1; }
    </style>
</head>
<body>

    <div id="ecoa-toast-container"></div>

    <div class="auth-card">
        <div class="auth-logo">Ecoa<span>.</span></div>
        <div class="auth-subtitle">{{ $subtitle ?? 'Acesso ao sistema' }}</div>

        {{ $slot }}
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>

    <script>
        // Sistema simples de toast, sem dependência externa além do jQuery
        // que o projeto já usa. Mostra a mensagem, anima a entrada, e
        // remove sozinho depois de alguns segundos.
        function ecoaToast(mensagem, tipo) {
            const container = document.getElementById('ecoa-toast-container');
            const icone = tipo === 'success' ? '✓' : '!';

            const toast = document.createElement('div');
            toast.className = 'ecoa-toast ' + tipo;
            toast.innerHTML = `
                <span class="icon">${icone}</span>
                <span>${mensagem}</span>
                <button class="close-btn" aria-label="Fechar">&times;</button>
            `;

            container.appendChild(toast);
            requestAnimationFrame(() => toast.classList.add('show'));

            const remover = () => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            };

            toast.querySelector('.close-btn').addEventListener('click', remover);
            setTimeout(remover, 6000);
        }

        document.addEventListener('DOMContentLoaded', function () {
            @if (session('status'))
                ecoaToast(@json(session('status')), 'success');
            @endif

            @if ($errors->any())
                ecoaToast(@json($errors->first()), 'error');
            @endif
        });
    </script>

</body>
</html>