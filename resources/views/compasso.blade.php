<!DOCTYPE html>

<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Compasso Livre</title>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    html {
        scroll-behavior: smooth;
    }

    body {
        font-family: Arial, Helvetica, sans-serif;
        background: #0b1020;
        color: #ffffff;
        min-height: 100vh;
    }

    /* =========================
       HEADER
    ========================= */

    header {
        width: 100%;
        padding: 22px 7%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        background: rgba(11, 16, 32, 0.9);
        backdrop-filter: blur(12px);
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .logo {
        font-size: 25px;
        font-weight: 800;
        letter-spacing: -1px;
    }

    .logo span {
        color: #ff6b35;
    }

    nav {
        display: flex;
        gap: 30px;
    }

    nav a {
        color: #b9bfd3;
        text-decoration: none;
        font-size: 15px;
        transition: 0.3s;
    }

    nav a:hover {
        color: #ff6b35;
    }

    /* =========================
       HERO
    ========================= */

    .hero {
        min-height: calc(100vh - 80px);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 70px 7%;
        gap: 60px;
        position: relative;
        overflow: hidden;
    }

    .hero::before {
        content: "";
        position: absolute;
        width: 500px;
        height: 500px;
        background: #ff6b35;
        opacity: 0.07;
        filter: blur(120px);
        border-radius: 50%;
        right: 5%;
        top: 10%;
        pointer-events: none;
    }

    .hero-content {
        max-width: 620px;
        position: relative;
        z-index: 2;
    }

    .tag {
        display: inline-block;
        padding: 8px 15px;
        border-radius: 30px;
        background: rgba(255, 107, 53, 0.12);
        border: 1px solid rgba(255, 107, 53, 0.2);
        color: #ff8a5b;
        font-size: 13px;
        font-weight: bold;
        margin-bottom: 25px;
    }

    h1 {
        font-size: clamp(48px, 7vw, 82px);
        line-height: 0.95;
        letter-spacing: -4px;
        margin-bottom: 25px;
    }

    h1 span {
        color: #ff6b35;
    }

    .description {
        color: #aeb5ca;
        font-size: 18px;
        line-height: 1.7;
        max-width: 550px;
        margin-bottom: 35px;
    }

    /* =========================
       BUTTONS
    ========================= */

    .buttons {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .btn {
        padding: 15px 25px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: bold;
        transition: 0.3s;
    }

    .primary {
        background: #ff6b35;
        color: white;
        box-shadow: 0 10px 30px rgba(255, 107, 53, 0.18);
    }

    .primary:hover {
        background: #e85520;
        transform: translateY(-2px);
        box-shadow: 0 15px 35px rgba(255, 107, 53, 0.25);
    }

    .secondary {
        border: 1px solid #30374d;
        color: white;
    }

    .secondary:hover {
        border-color: #ff6b35;
        color: #ff8a5b;
    }

    /* =========================
       MUSIC CARD
    ========================= */

    .music-card {
        width: 390px;
        min-height: 390px;
        background: linear-gradient(145deg, #151b32, #0f1427);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 25px;
        padding: 35px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        box-shadow: 0 30px 80px rgba(0, 0, 0, 0.35);
        position: relative;
        z-index: 2;
    }

    .music-card::before {
        content: "";
        position: absolute;
        inset: 0;
        border-radius: 25px;
        background: linear-gradient(
            135deg,
            rgba(255, 107, 53, 0.08),
            transparent 50%
        );
        pointer-events: none;
    }

    .disc {
        width: 180px;
        height: 180px;
        margin: 0 auto 30px;
        border-radius: 50%;
        background:
            repeating-radial-gradient(
                circle,
                #171717 0,
                #171717 5px,
                #242424 6px,
                #242424 8px
            );
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 0 50px rgba(255, 107, 53, 0.18);
        animation: rotateDisc 12s linear infinite;
    }

    .disc::after {
        content: "♫";
        width: 55px;
        height: 55px;
        border-radius: 50%;
        background: #ff6b35;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 25px;
        animation: counterRotate 12s linear infinite;
    }

    @keyframes rotateDisc {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    @keyframes counterRotate {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(-360deg);
        }
    }

    .card-title {
        text-align: center;
        font-size: 20px;
        margin-bottom: 8px;
    }

    .card-text {
        text-align: center;
        color: #858da5;
        font-size: 14px;
    }

    /* =========================
       FEATURES
    ========================= */

    .features {
        padding: 100px 7%;
        background: #080c18;
    }

    .features h2 {
        font-size: 38px;
        margin-bottom: 15px;
    }

    .features > p {
        color: #858da5;
        margin-bottom: 40px;
    }

    .cards {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }

    .feature {
        padding: 30px;
        background: #10162a;
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 18px;
        transition: 0.3s;
    }

    .feature:hover {
        transform: translateY(-5px);
        border-color: rgba(255, 107, 53, 0.35);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    }

    .icon {
        font-size: 30px;
        margin-bottom: 20px;
    }

    .feature h3 {
        margin-bottom: 10px;
        font-size: 20px;
    }

    .feature p {
        color: #858da5;
        line-height: 1.6;
        font-size: 14px;
    }

    /* =========================
       FOOTER
    ========================= */

    footer {
        padding: 30px 7%;
        background: #080c18;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
        color: #666f86;
        font-size: 14px;
        text-align: center;
    }

    footer span {
        color: #ff6b35;
    }

    /* =========================
       RESPONSIVO
    ========================= */

    @media (max-width: 850px) {

        nav {
            display: none;
        }

        .hero {
            flex-direction: column;
            align-items: flex-start;
            padding-top: 60px;
            padding-bottom: 80px;
        }

        .hero-content {
            max-width: 100%;
        }

        h1 {
            letter-spacing: -3px;
        }

        .music-card {
            width: 100%;
            max-width: 390px;
            align-self: center;
        }

        .cards {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 500px) {

        header {
            padding: 20px 5%;
        }

        .hero {
            padding: 50px 5%;
        }

        h1 {
            font-size: 50px;
        }

        .description {
            font-size: 16px;
        }

        .buttons {
            flex-direction: column;
        }

        .btn {
            text-align: center;
        }

        .features {
            padding: 70px 5%;
        }

        .features h2 {
            font-size: 32px;
        }
    }
</style>

</head>

<body>

<header>

    <div class="logo">
        Compasso <span>Livre</span>
    </div>

    <nav>
        <a href="/">Início</a>
        <a href="#sobre">Sobre</a>
        <a href="#recursos">Recursos</a>
        <a href="/login">Entrar</a>
    </nav>

</header>


<main>

    <!-- HERO -->

    <section class="hero">

        <div class="hero-content">

            <span class="tag">
                🎵 APRENDER • CRIAR • EXPERIMENTAR
            </span>

            <h1>
                Aprenda música<br>
                de um jeito <span>livre.</span>
            </h1>

            <p class="description">
                O Compasso Livre transforma o aprendizado musical
                em uma experiência prática, interativa e criativa.
                Aprenda, experimente e crie sua própria música.
            </p>

            <div class="buttons">

                <a href="/cadastro" class="btn primary">
                    Começar agora
                </a>

                <a href="#recursos" class="btn secondary">
                    Conhecer o projeto
                </a>

            </div>

        </div>


        <div class="music-card">

            <div class="disc"></div>

            <h3 class="card-title">
                Sua música começa aqui.
            </h3>

            <p class="card-text">
                Explore ritmos, notas, acordes e composição.
            </p>

        </div>

    </section>


    <!-- RECURSOS -->

    <section class="features" id="recursos">

        <h2>
            Aprender fazendo.
        </h2>

        <p>
            Recursos pensados para tornar o aprendizado musical
            mais prático e interessante.
        </p>


        <div class="cards">

            <div class="feature">

                <div class="icon">
                    🥁
                </div>

                <h3>
                    Ritmo
                </h3>

                <p>
                    Aprenda conceitos de ritmo e compasso através
                    de atividades interativas.
                </p>

            </div>


            <div class="feature">

                <div class="icon">
                    🎹
                </div>

                <h3>
                    Harmonia
                </h3>

                <p>
                    Experimente notas e acordes e entenda como
                    eles funcionam juntos.
                </p>

            </div>


            <div class="feature">

                <div class="icon">
                    🎼
                </div>

                <h3>
                    Criação
                </h3>

                <p>
                    Coloque o conhecimento em prática criando
                    suas próprias sequências musicais.
                </p>

            </div>

        </div>

    </section>


    <!-- SOBRE -->

    <section class="features" id="sobre">

        <h2>
            Música na prática.
        </h2>

        <p>
            No Compasso Livre, o aluno não fica apenas lendo
            conteúdos sobre música. Ele aprende experimentando,
            criando e descobrindo na prática.
        </p>

    </section>

</main>


<footer>

    <p>
        © 2026 <span>Compasso Livre</span>.
        Aprender música pode ser livre.
    </p>

</footer>

</body>

</html>
