<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel - Ecoa</title> <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,400;0,6..72,500;0,6..72,600;1,6..72,500&family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <style>
    :root {
        --ink: #16231f;
        --paper: #f4f6f2;
        --paper-dim: #eceee8;
        --teal-900: #0d3634;
        --teal-800: #124542;
        --teal-700: #1b5e5a;
        --teal-600: #256f6a;
        --amber: #c6873a;
        --amber-soft: #e7c396;
        --sage: #b9cfc0;
        --white: #ffffff;
        --line: rgba(22, 35, 31, 0.12);
        --line-dark: rgba(244, 246, 242, 0.14);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    html {
        scroll-behavior: smooth;
    }

    body {
        background: var(--paper);
        color: var(--ink);
        font-family: 'IBM Plex Sans', system-ui, sans-serif;
        -webkit-font-smoothing: antialiased;
        line-height: 1.5;
    }

    a {
        color: inherit;
        text-decoration: none;
    }

    button {
        font-family: inherit;
    }

    .container {
        max-width: 1180px;
        margin: 0 auto;
        padding: 0 32px;
    }

    h1,
    h2,
    h3 {
        font-family: 'Newsreader', Georgia, serif;
        font-weight: 500;
        letter-spacing: -0.01em;
    }

    /* ========================= HEADER ========================= */
    header {
        position: sticky;
        top: 0;
        z-index: 50;
        background: rgba(13, 54, 52, 0.96);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid var(--line-dark);
    }

    .nav {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 0;
    }

    .wordmark {
        font-family: 'Newsreader', Georgia, serif;
        font-size: 24px;
        font-weight: 600;
        color: var(--paper);
        display: flex;
        align-items: baseline;
        gap: 2px;
    }

    .wordmark span {
        color: var(--amber);
    }

    .nav-right {
        display: flex;
        align-items: center;
        gap: 18px;
    }

    .user-name {
        color: rgba(244, 246, 242, 0.85);
        font-size: 14px;
    }

    /* ========================= LOGOUT ========================= */
    .logout-form {
        margin: 0;
    }

    .logout-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--amber);
        color: #241505;
        border: none;
        border-radius: 8px;
        padding: 10px 18px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
    }

    .logout-btn:hover {
        transform: translateY(-1px);
        background: #d29648;
        box-shadow: 0 8px 20px rgba(198, 135, 58, 0.25);
    }

    .logout-btn:focus-visible {
        outline: 2px solid var(--paper);
        outline-offset: 3px;
    }

    /* ========================= HERO ========================= */
    .hero {
        background: radial-gradient(720px 420px at 82% -10%, rgba(198, 135, 58, 0.16), transparent 60%), linear-gradient(180deg, var(--teal-900), #0a2b29 88%);
        color: var(--paper);
        padding: 78px 0 84px;
    }

    .hero-content {
        max-width: 760px;
    }

    .eyebrow {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 12px;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        gap: 9px;
        color: var(--sage);
        margin-bottom: 18px;
    }

    .eyebrow::before {
        content: "";
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: var(--amber);
        box-shadow: 0 0 0 3px rgba(198, 135, 58, 0.22);
    }

    .hero h1 {
        font-size: clamp(40px, 5vw, 58px);
        line-height: 1.06;
        margin-bottom: 16px;
        color: var(--paper);
    }

    .hero p {
        color: rgba(244, 246, 242, 0.75);
        font-size: 17px;
        max-width: 55ch;
    }

    /* ========================= DASHBOARD ========================= */
    .dashboard {
        padding: 60px 0 80px;
    }

    .section-heading {
        margin-bottom: 28px;
    }

    .section-heading h2 {
        font-size: 30px;
        color: var(--ink);
        margin-bottom: 6px;
    }

    .section-heading p {
        color: #5b6660;
        font-size: 14.5px;
    }

    /* ========================= CARDS ========================= */
    .cards {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
        margin-bottom: 42px;
    }

    .card {
        background: var(--white);
        border: 1px solid var(--line);
        border-radius: 14px;
        padding: 26px;
        box-shadow: 0 8px 24px rgba(22, 35, 31, 0.05);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(22, 35, 31, 0.08);
    }

    .card h3 {
        font-family: 'IBM Plex Sans', sans-serif;
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #68736d;
        margin-bottom: 12px;
    }

    .value {
        font-family: 'Newsreader', Georgia, serif;
        font-size: 42px;
        line-height: 1;
        color: var(--teal-700);
    }

    /* ========================= PROFILE ========================= */
    .profile {
        background: var(--white);
        border: 1px solid var(--line);
        border-radius: 14px;
        padding: 32px;
        box-shadow: 0 8px 24px rgba(22, 35, 31, 0.05);
    }

    .profile-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding-bottom: 20px;
        margin-bottom: 22px;
        border-bottom: 1px solid var(--line);
    }

    .profile h2 {
        font-size: 28px;
        color: var(--ink);
    }

    .profile-label {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--teal-600);
    }

    .profile-info {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
    }

    .profile-item {
        padding: 18px;
        background: var(--paper);
        border-radius: 10px;
    }

    .profile-item span {
        display: block;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #68736d;
        margin-bottom: 6px;
    }

    .profile-item strong {
        display: block;
        font-size: 15px;
        font-weight: 600;
        color: var(--ink);
        word-break: break-word;
    }

    /* ========================= FOOTER ========================= */
    footer {
        background: #0a2b29;
        color: rgba(244, 246, 242, 0.55);
        padding: 36px 0;
        font-size: 13.5px;
    }

    .footer-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
    }

    .footer-links {
        display: flex;
        gap: 22px;
    }

    .footer-links a {
        color: rgba(244, 246, 242, 0.55);
        transition: color 0.2s ease;
    }

    .footer-links a:hover {
        color: var(--paper);
    }

    /* ========================= RESPONSIVE ========================= */
    @media (max-width: 900px) {
        .cards {
            grid-template-columns: repeat(2, 1fr);
        }

        .profile-info {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 650px) {
        .container {
            padding: 0 20px;
        }

        .nav {
            padding: 16px 0;
        }

        .user-name {
            display: none;
        }

        .hero {
            padding: 60px 0 64px;
        }

        .hero h1 {
            font-size: 40px;
        }

        .hero p {
            font-size: 16px;
        }

        .dashboard {
            padding: 45px 0 60px;
        }

        .cards {
            grid-template-columns: 1fr;
        }

        .profile {
            padding: 24px;
        }

        .profile-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .footer-row {
            flex-direction: column;
            align-items: flex-start;
        }
    }
    </style>
</head>

<body>
    <!-- ========================= HEADER ========================== -->
    <header>
        <div class="container">
            <div class="nav"> <a href="{{ route('landing') }}" class="wordmark"> Ecoa<span>.</span> </a>
                <div class="nav-right"> <span class="user-name"> {{ auth()->user()->name }} </span>
                    <form method="POST" action="{{ route('logout') }}" class="logout-form"> @csrf <button type="submit"
                            class="logout-btn"> Sair </button> </form>
                </div>
            </div>
        </div>
    </header> <!-- ========================= HERO ========================== -->
    <section class="hero">
        <div class="container">
            <div class="hero-content"> <span class="eyebrow"> Painel Ecoa </span>
                <h1> Painel Principal </h1>
                <p> Bem-vindo ao sistema Ecoa. Aqui você acompanha as informações do acompanhamento fonoaudiológico.
                </p>
            </div>
        </div>
    </section> <!-- ========================= DASHBOARD ========================== -->
    <main class="dashboard">
        <div class="container">
            <div class="section-heading">
                <h2> Visão geral </h2>
                <p> Resumo das informações cadastradas no sistema. </p>
            </div> <!-- CARDS -->
            <div class="cards">
                <div class="card">
                    <h3> Crianças </h3>
                    <div class="value"> 0 </div>
                </div>
                <div class="card">
                    <h3> Planos Terapêuticos </h3>
                    <div class="value"> 0 </div>
                </div>
                <div class="card">
                    <h3> Exercícios </h3>
                    <div class="value"> 0 </div>
                </div>
                <div class="card">
                    <h3> Profissionais </h3>
                    <div class="value"> 0 </div>
                </div>
            </div> <!-- PERFIL -->
            <div class="profile">
                <div class="profile-header">
                    <h2> Usuário autenticado </h2> <span class="profile-label"> Sessão ativa </span>
                </div>
                <div class="profile-info">
                    <div class="profile-item"> <span> Nome </span> <strong> {{ auth()->user()->name }} </strong> </div>
                    <div class="profile-item"> <span> E-mail </span> <strong> {{ auth()->user()->email }} </strong>
                    </div>
                    <div class="profile-item"> <span> ID </span> <strong> {{ auth()->user()->id }} </strong> </div>
                </div>
            </div>
        </div>
    </main> <!-- ========================= FOOTER ========================== -->
    <footer>
        <div class="container">
            <div class="footer-row"> <span> © 2026 Ecoa — Sistema de Acompanhamento Fonoaudiológico Infantil </span>
                <div class="footer-links"> <a href="{{ route('landing') }}"> Início </a> <a href="{{ route('home') }}">
                        Painel </a> </div>
            </div>
        </div>
    </footer>
</body>

</html>