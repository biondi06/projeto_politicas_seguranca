{{--
    Layout compartilhado para as telas de autenticação (login, registro,
    recuperação de senha, 2FA). O CSS vem do app.css global (classes
    .auth-*, .btn-ecoa, .ecoa-toast) — antes vivia inline neste arquivo.
--}}
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Ecoa' }}</title>

    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Newsreader:opsz,wght@6..72,400;6..72,500;6..72,600&family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>
<body class="auth-body">

    <div id="ecoa-toast-container"></div>

    <div class="auth-card">
        <div class="auth-logo">
            <img src="{{ asset('img/ecoa-icone.png') }}" alt="">
            Ecoa<span>.</span>
        </div>
        <div class="auth-subtitle">{{ $subtitle ?? 'Acesso ao sistema' }}</div>

        {{ $slot }}
    </div>

    <script>
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
