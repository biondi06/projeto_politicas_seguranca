<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ecoa — Acompanhamento fonoaudiológico infantil</title>

<link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
<link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link
    href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,400;0,6..72,500;0,6..72,600;1,6..72,500&family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap"
    rel="stylesheet">

@vite(['resources/css/app.css'])
</head>
<body>

<header>
    <div class="container">
        <div class="nav">
            <a href="{{ route('landing') }}" class="wordmark">
                <img src="{{ asset('img/ecoa-icone.png') }}" alt="">
                Ecoa<span>.</span>
            </a>
            <nav class="nav-right" style="gap:28px;">
                <a href="#ciclo" class="security-link" style="padding:0; background:none; border:none;"><span>Como funciona</span></a>
                <a href="#recursos" class="security-link" style="padding:0; background:none; border:none;"><span>Recursos</span></a>
                <a href="#perfis" class="security-link" style="padding:0; background:none; border:none;"><span>Para quem é</span></a>
                <a href="#seguranca" class="security-link" style="padding:0; background:none; border:none;"><span>Segurança</span></a>
            </nav>
            <a href="{{ route('login') }}" class="landing-btn landing-btn-ghost" style="padding:9px 18px; font-size:14px;">Entrar</a>
        </div>
    </div>
</header>

<main>

    {{-- HERO --}}
    <section class="landing-hero">
        <div class="container landing-hero-grid">
            <div>
                <span class="eyebrow">Acompanhamento fonoaudiológico infantil</span>
                <h1>A fala de cada criança, <em>acompanhada em rede.</em></h1>
                <p class="lede">
                    O Ecoa conecta fonoaudiólogo, pediatra e terapeuta ocupacional em torno de um único
                    plano terapêutico — com exercícios que a família reproduz em casa e uma evolução
                    que fica registrada, não perdida em anotações soltas.
                </p>
                <div class="landing-cta-row">
                    <a href="{{ route('register') }}" class="landing-btn landing-btn-primary">Solicitar acesso</a>
                    <a href="#ciclo" class="landing-btn landing-btn-ghost">Ver como funciona</a>
                </div>
            </div>

            <div style="position:relative; height:420px; display:flex; align-items:center; justify-content:center;" aria-hidden="true">
                <svg class="echo-rings" viewBox="0 0 400 400" style="width:100%; max-width:400px;">
                    <circle class="ring-a pulse-1" cx="200" cy="200" r="60"/>
                    <circle class="ring-b pulse-2" cx="200" cy="200" r="60"/>
                    <circle class="ring-c pulse-3" cx="200" cy="200" r="60"/>
                    <circle cx="200" cy="200" r="7" fill="var(--amber)"/>
                    <text x="248" y="130" style="font-family:'IBM Plex Mono',monospace; font-size:13px; fill:rgba(244,246,242,.6);">/pa/</text>
                    <text x="90" y="150" style="font-family:'IBM Plex Mono',monospace; font-size:13px; fill:rgba(244,246,242,.6);">/s/</text>
                    <text x="270" y="290" style="font-family:'IBM Plex Mono',monospace; font-size:13px; fill:rgba(244,246,242,.6);">/ʁ/</text>
                    <text x="80" y="270" style="font-family:'IBM Plex Mono',monospace; font-size:13px; fill:rgba(244,246,242,.6);">/l/</text>
                </svg>
            </div>
        </div>
    </section>

    {{-- PROOF STRIP --}}
    <div class="landing-proof">
        <div class="container landing-proof-grid">
            <div class="landing-proof-item">
                <div class="landing-proof-num">1 plano</div>
                <div class="landing-proof-label">por criança, comentado por todos os profissionais envolvidos</div>
            </div>
            <div class="landing-proof-item">
                <div class="landing-proof-num">Exercícios</div>
                <div class="landing-proof-label">reaproveitados entre casos, não recriados a cada atendimento</div>
            </div>
            <div class="landing-proof-item">
                <div class="landing-proof-num">Dado sensível</div>
                <div class="landing-proof-label">tratado com criptografia e controle de acesso por perfil</div>
            </div>
        </div>
    </div>

    {{-- CICLO --}}
    <section class="landing-block" id="ciclo">
        <div class="container">
            <span class="eyebrow" style="color:var(--teal-600);">O ciclo do cuidado</span>
            <h2>Um plano vivo, não uma ficha parada na gaveta.</h2>
            <ul style="list-style:none; max-width:600px;">
                <li style="display:grid; grid-template-columns:26px 1fr; gap:14px; padding:18px 0; border-top:1px solid var(--line);">
                    <span style="font-family:'IBM Plex Mono',monospace; color:var(--amber); font-size:13px;">01</span>
                    <div><h4 style="font-size:16px; margin-bottom:5px;">Avaliar</h4><p style="font-size:14.5px; color:#4c5750;">O fonoaudiólogo define fonemas-alvo, dificuldades e metas do plano terapêutico.</p></div>
                </li>
                <li style="display:grid; grid-template-columns:26px 1fr; gap:14px; padding:18px 0; border-top:1px solid var(--line);">
                    <span style="font-family:'IBM Plex Mono',monospace; color:var(--amber); font-size:13px;">02</span>
                    <div><h4 style="font-size:16px; margin-bottom:5px;">Praticar</h4><p style="font-size:14.5px; color:#4c5750;">A família reproduz em casa os exercícios da biblioteca, já ajustados ao caso.</p></div>
                </li>
                <li style="display:grid; grid-template-columns:26px 1fr; gap:14px; padding:18px 0; border-top:1px solid var(--line);">
                    <span style="font-family:'IBM Plex Mono',monospace; color:var(--amber); font-size:13px;">03</span>
                    <div><h4 style="font-size:16px; margin-bottom:5px;">Registrar</h4><p style="font-size:14.5px; color:#4c5750;">Sessões e exercícios em casa alimentam o diário de evolução da criança.</p></div>
                </li>
                <li style="display:grid; grid-template-columns:26px 1fr; gap:14px; padding:18px 0; border-top:1px solid var(--line);">
                    <span style="font-family:'IBM Plex Mono',monospace; color:var(--amber); font-size:13px;">04</span>
                    <div><h4 style="font-size:16px; margin-bottom:5px;">Ajustar</h4><p style="font-size:14.5px; color:#4c5750;">Fonoaudiólogo, pediatra e terapeuta ocupacional revisam o plano juntos — e o ciclo recomeça.</p></div>
                </li>
            </ul>
        </div>
    </section>

    {{-- RECURSOS --}}
    <section class="landing-block" id="recursos" style="background:var(--paper-dim); border-top:1px solid var(--line); border-bottom:1px solid var(--line);">
        <div class="container">
            <span class="eyebrow" style="color:var(--teal-600);">Recursos essenciais</span>
            <h2>O que o Ecoa organiza no dia a dia da clínica.</h2>
            <p class="intro">Quatro peças que já existem, de forma dispersa, na rotina de qualquer equipe de fonoaudiologia infantil — aqui, conectadas em um único lugar.</p>

            <div class="landing-features-grid">
                <div class="landing-feature-card">
                    <span class="landing-feature-tag">Plano terapêutico</span>
                    <h3>Plano colaborativo</h3>
                    <p>Fonoaudiólogo, pediatra e terapeuta ocupacional comentam e ajustam o mesmo plano — sem decisão isolada de um só profissional.</p>
                </div>
                <div class="landing-feature-card">
                    <span class="landing-feature-tag">Biblioteca</span>
                    <h3>Exercícios de estimulação</h3>
                    <p>Catalogados por fonema e dificuldade, com instruções claras para os pais reproduzirem em casa corretamente.</p>
                </div>
                <div class="landing-feature-card">
                    <span class="landing-feature-tag">Acompanhamento</span>
                    <h3>Diário de evolução</h3>
                    <p>Progresso das sessões e dos exercícios em casa, relatado pelo profissional ou pela família, sempre com data e origem.</p>
                </div>
                <div class="landing-feature-card">
                    <span class="landing-feature-tag">Gestão</span>
                    <h3>Painel do coordenador</h3>
                    <p>Visão consolidada de quantas crianças estão em acompanhamento e quais planos aguardam revisão.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- PERFIS --}}
    <section class="landing-block landing-roles-band" id="perfis">
        <div class="container">
            <span class="eyebrow" style="color:var(--teal-600);">Acesso por perfil</span>
            <h2>Cada pessoa vê exatamente o que precisa.</h2>
            <p class="intro" style="color:rgba(244,246,242,.72);">Nada além disso — por princípio, não por limitação técnica.</p>

            <div class="landing-roles-grid">
                <div class="landing-role-card">
                    <span class="landing-role-eyebrow">Responsável pela terapia</span>
                    <h4>Fonoaudiólogo</h4>
                    <p>Cria e ajusta o plano, acessa a biblioteca e registra a evolução dos seus pacientes.</p>
                </div>
                <div class="landing-role-card">
                    <span class="landing-role-eyebrow">Especialista colaborador</span>
                    <h4>Pediatra / T.O.</h4>
                    <p>Comenta e sugere ajustes no plano terapêutico dos casos que acompanha.</p>
                </div>
                <div class="landing-role-card">
                    <span class="landing-role-eyebrow">Gestão clínica</span>
                    <h4>Coordenador clínico</h4>
                    <p>Leitura consolidada de planos e registros, com alertas de acompanhamento pendente.</p>
                </div>
                <div class="landing-role-card">
                    <span class="landing-role-eyebrow">Família</span>
                    <h4>Responsável legal</h4>
                    <p>Acompanha exercícios e relatórios da criança — sem acesso a dados clínicos de terceiros.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- SEGURANÇA --}}
    <section class="landing-block" id="seguranca">
        <div class="container landing-security-wrap">
            <div>
                <span class="eyebrow" style="color:var(--teal-600);">Dado sensível, tratado como tal</span>
                <h2>Segurança pensada para dado de saúde infantil.</h2>
                <p class="intro" style="margin-bottom:0;">Informação sobre desenvolvimento da fala é dado pessoal sensível. O Ecoa foi desenhado para isso desde a primeira linha de código, não como reforço depois.</p>
            </div>
            <ul class="landing-security-list">
                <li><span class="check">＋</span> Autenticação com verificação em duas etapas</li>
                <li><span class="check">＋</span> Senhas protegidas por hash com salt único por usuário</li>
                <li><span class="check">＋</span> Dados sensíveis criptografados em repouso e em trânsito</li>
                <li><span class="check">＋</span> Acesso restrito por perfil, por necessidade de atendimento</li>
                <li><span class="check">＋</span> Trilha de auditoria para toda alteração em registros clínicos</li>
            </ul>
        </div>
    </section>

    {{-- CLOSING --}}
    <section class="landing-closing">
        <div class="container">
            <span class="eyebrow" style="color:var(--sage); justify-content:center;">Comece com uma turma piloto</span>
            <h2>Leve o Ecoa para a sua clínica ou setor de fonoaudiologia.</h2>
            <p>Sem substituir o trabalho clínico — só organizando o que já acontece entre profissionais, todos os dias.</p>
            <div class="landing-cta-row">
                <a href="{{ route('register') }}" class="landing-btn landing-btn-primary">Solicitar acesso</a>
                <a href="mailto:fonoaudiologiaecoa@gmail.com" class="landing-btn landing-btn-ghost">Falar com a equipe</a>
            </div>
        </div>
    </section>

</main>

<footer>
    <div class="container footer-row">
        <div class="footer-brand">
            <img src="{{ asset('img/ecoa-icone.png') }}" alt="">
            <span>© {{ date('Y') }} Ecoa — Sistema de Acompanhamento Fonoaudiológico Infantil</span>
        </div>
        <div class="footer-links">
            <a href="#ciclo">Como funciona</a>
            <a href="#seguranca">Segurança</a>
            <a href="{{ route('login') }}">Entrar</a>
        </div>
    </div>
</footer>

</body>
</html>
