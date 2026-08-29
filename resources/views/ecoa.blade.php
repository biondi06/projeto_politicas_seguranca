<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ecoa — Acompanhamento fonoaudiológico infantil</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,400;0,6..72,500;0,6..72,600;1,6..72,500&family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
  :root{
    --ink:#16231f;
    --paper:#f4f6f2;
    --paper-dim:#eceee8;
    --teal-900:#0d3634;
    --teal-800:#124542;
    --teal-700:#1b5e5a;
    --teal-600:#256f6a;
    --amber:#c6873a;
    --amber-soft:#e7c396;
    --sage:#b9cfc0;
    --white:#ffffff;
    --line:rgba(22,35,31,0.12);
    --line-dark:rgba(244,246,242,0.14);
  }

  *{margin:0;padding:0;box-sizing:border-box;}
  html{scroll-behavior:smooth;}
  body{
    background:var(--paper);
    color:var(--ink);
    font-family:'IBM Plex Sans', system-ui, sans-serif;
    -webkit-font-smoothing:antialiased;
    line-height:1.5;
  }
  a{color:inherit;}
  img,svg{display:block;max-width:100%;}
  .container{max-width:1180px;margin:0 auto;padding:0 32px;}

  h1,h2,h3{
    font-family:'Newsreader', Georgia, serif;
    font-weight:500;
    letter-spacing:-0.01em;
    color:inherit;
  }
  em{font-style:italic;color:var(--amber-soft);}
  .eyebrow{
    font-family:'IBM Plex Mono', monospace;
    font-size:12.5px;
    letter-spacing:0.14em;
    text-transform:uppercase;
    display:inline-flex;
    align-items:center;
    gap:9px;
  }
  .eyebrow::before{
    content:"";
    width:7px;height:7px;
    border-radius:50%;
    background:var(--amber);
    box-shadow:0 0 0 3px rgba(198,135,58,0.22);
  }

  a:focus-visible, button:focus-visible{
    outline:2px solid var(--amber);
    outline-offset:3px;
    border-radius:4px;
  }

  /* ============ NAV ============ */
  header{
    position:sticky; top:0; z-index:50;
    background:rgba(13,54,52,0.94);
    backdrop-filter:blur(10px);
    border-bottom:1px solid var(--line-dark);
  }
  .nav{
    display:flex; align-items:center; justify-content:space-between;
    padding:20px 32px;
    max-width:1180px; margin:0 auto;
    color:var(--paper);
  }
  .wordmark{
    font-family:'Newsreader', serif;
    font-size:23px; font-weight:600;
    color:var(--paper);
    display:flex; align-items:baseline; gap:2px;
  }
  .wordmark .dot{color:var(--amber);}
  .nav-links{display:flex; align-items:center; gap:34px;}
  .nav-links a{
    font-size:14.5px; color:rgba(244,246,242,0.72);
    text-decoration:none; transition:color .2s;
  }
  .nav-links a:hover{color:var(--paper);}
  .nav-cta{
    font-size:14px; font-weight:600;
    padding:9px 18px; border-radius:8px;
    border:1px solid rgba(244,246,242,0.3);
    text-decoration:none;
    transition:border-color .2s, background .2s;
  }
  .nav-cta:hover{border-color:var(--amber); background:rgba(198,135,58,0.12);}
  .nav-toggle{display:none;}

  /* ============ HERO ============ */
  .hero{
    background:
      radial-gradient(720px 420px at 82% -10%, rgba(198,135,58,0.16), transparent 60%),
      linear-gradient(180deg, var(--teal-900), #0a2b29 88%);
    color:var(--paper);
    padding:96px 0 84px;
    position:relative;
    overflow:hidden;
  }
  .hero-grid{
    display:grid;
    grid-template-columns:1.05fr 0.95fr;
    gap:56px;
    align-items:center;
  }
  .hero-eyebrow{color:var(--sage);}
  .hero h1{
    font-size:clamp(38px, 4.6vw, 58px);
    line-height:1.06;
    margin:22px 0 22px;
    max-width:15ch;
  }
  .hero p.lede{
    font-size:17.5px;
    color:rgba(244,246,242,0.78);
    max-width:46ch;
    margin-bottom:34px;
  }
  .cta-row{display:flex; gap:14px; flex-wrap:wrap;}
  .btn{
    display:inline-flex; align-items:center; gap:8px;
    padding:14px 24px;
    border-radius:9px;
    font-size:15px; font-weight:600;
    text-decoration:none;
    transition:transform .15s ease, box-shadow .15s ease, background .15s ease;
  }
  .btn-primary{
    background:var(--amber); color:#241505;
    box-shadow:0 12px 28px rgba(198,135,58,0.28);
  }
  .btn-primary:hover{transform:translateY(-1px); box-shadow:0 16px 34px rgba(198,135,58,0.36);}
  .btn-ghost{
    border:1px solid rgba(244,246,242,0.28);
    color:var(--paper);
  }
  .btn-ghost:hover{border-color:var(--paper);}

  /* signature: echo rings */
  .echo-wrap{
    position:relative;
    height:420px;
    display:flex; align-items:center; justify-content:center;
  }
  .echo-rings{width:100%; max-width:400px; overflow:visible;}
  .echo-rings circle{
    fill:none;
    stroke-width:1.4;
  }
  .ring-a{stroke:rgba(185,207,192,0.55);}
  .ring-b{stroke:rgba(198,135,58,0.55);}
  .ring-c{stroke:rgba(244,246,242,0.35);}
  @media (prefers-reduced-motion: no-preference){
    .pulse-1{animation:pulse 4.5s ease-out infinite;}
    .pulse-2{animation:pulse 4.5s ease-out 1.5s infinite;}
    .pulse-3{animation:pulse 4.5s ease-out 3s infinite;}
  }
  @keyframes pulse{
    0%{ transform:scale(0.55); opacity:0; }
    12%{ opacity:1; }
    100%{ transform:scale(1); opacity:0; }
  }
  .echo-core{fill:var(--amber);}
  .phoneme{
    font-family:'IBM Plex Mono', monospace;
    font-size:13px;
    fill:rgba(244,246,242,0.6);
  }

  /* ============ PROOF STRIP ============ */
  .proof{
    background:var(--paper-dim);
    border-bottom:1px solid var(--line);
  }
  .proof-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    padding:34px 0;
  }
  .proof-item{
    text-align:center;
    padding:0 20px;
    border-left:1px solid var(--line);
  }
  .proof-item:first-child{border-left:none;}
  .proof-num{
    font-family:'Newsreader', serif;
    font-size:30px;
    color:var(--teal-700);
  }
  .proof-label{
    font-size:13px;
    color:#5b6660;
    margin-top:4px;
  }

  /* ============ SECTION SHELL ============ */
  section.block{padding:96px 0;}
  .kicker{color:var(--teal-600);}
  .block h2{
    font-size:clamp(28px, 3.2vw, 38px);
    margin:16px 0 18px;
    max-width:18ch;
  }
  .block > .container > p.intro{
    color:#4c5750;
    max-width:56ch;
    font-size:16px;
    margin-bottom:56px;
  }

  /* ============ LOOP DIAGRAM ============ */
  .loop-wrap{
    display:grid;
    grid-template-columns:0.9fr 1.1fr;
    gap:60px;
    align-items:center;
  }
  .loop-diagram{width:100%; height:auto;}
  .loop-step-label{
    font-family:'IBM Plex Mono', monospace;
    font-size:12px;
    fill:var(--teal-800);
    letter-spacing:0.04em;
  }
  .loop-list{list-style:none;}
  .loop-list li{
    display:grid;
    grid-template-columns:26px 1fr;
    gap:14px;
    padding:18px 0;
    border-top:1px solid var(--line);
  }
  .loop-list li:first-child{border-top:none;}
  .loop-list .mark{
    font-family:'IBM Plex Mono', monospace;
    color:var(--amber);
    font-size:13px;
    padding-top:3px;
  }
  .loop-list h4{
    font-family:'IBM Plex Sans', sans-serif;
    font-weight:600;
    font-size:16px;
    margin-bottom:5px;
  }
  .loop-list p{font-size:14.5px; color:#4c5750;}

  /* ============ FEATURES ============ */
  .features-grid{
    display:grid;
    grid-template-columns:repeat(2, 1fr);
    gap:1px;
    background:var(--line);
    border:1px solid var(--line);
    border-radius:16px;
    overflow:hidden;
  }
  .feature-card{
    background:var(--white);
    padding:32px;
  }
  .feature-tag{
    font-family:'IBM Plex Mono', monospace;
    font-size:11.5px;
    letter-spacing:0.08em;
    text-transform:uppercase;
    color:var(--teal-600);
    margin-bottom:14px;
    display:block;
  }
  .feature-card h3{
    font-size:20px;
    margin-bottom:10px;
    font-weight:500;
  }
  .feature-card p{font-size:14.5px; color:#4c5750;}

  /* ============ ROLES ============ */
  .roles-band{
    background:var(--teal-900);
    color:var(--paper);
  }
  .roles-band .kicker{color:var(--sage);}
  .roles-band h2{color:var(--paper);}
  .roles-band > .container > p.intro{color:rgba(244,246,242,0.72);}
  .roles-grid{
    display:grid;
    grid-template-columns:repeat(4, 1fr);
    gap:24px;
  }
  .role-card{
    border:1px solid rgba(244,246,242,0.16);
    border-radius:14px;
    padding:26px 22px;
  }
  .role-card .role-eyebrow{
    font-family:'IBM Plex Mono', monospace;
    font-size:11px;
    color:var(--amber-soft);
    letter-spacing:0.06em;
    text-transform:uppercase;
  }
  .role-card h4{
    font-family:'Newsreader', serif;
    font-size:19px;
    font-weight:500;
    margin:10px 0 8px;
  }
  .role-card p{font-size:13.5px; color:rgba(244,246,242,0.68);}

  /* ============ SECURITY BAND ============ */
  .security{
    background:var(--paper);
    border-top:1px solid var(--line);
    border-bottom:1px solid var(--line);
  }
  .security-wrap{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:60px;
    align-items:start;
  }
  .security-list{list-style:none;}
  .security-list li{
    display:grid;
    grid-template-columns:20px 1fr;
    gap:12px;
    padding:14px 0;
    font-size:14.5px;
    color:#33423c;
    border-top:1px solid var(--line);
  }
  .security-list li:first-child{border-top:none;}
  .security-list .check{color:var(--teal-700); font-weight:700;}

  /* ============ CLOSING CTA ============ */
  .closing{
    background:linear-gradient(180deg, #0a2b29, var(--teal-900));
    color:var(--paper);
    text-align:center;
    padding:100px 0;
  }
  .closing h2{
    color:var(--paper);
    font-size:clamp(30px,4vw,44px);
    max-width:20ch;
    margin:0 auto 18px;
  }
  .closing p{
    color:rgba(244,246,242,0.72);
    max-width:48ch;
    margin:0 auto 34px;
    font-size:16.5px;
  }
  .closing .cta-row{justify-content:center;}

  /* ============ FOOTER ============ */
  footer{
    background:#0a2b29;
    color:rgba(244,246,242,0.55);
    padding:40px 0;
    font-size:13.5px;
  }
  .footer-row{
    display:flex; justify-content:space-between; align-items:center;
    flex-wrap:wrap; gap:14px;
  }
  .footer-links{display:flex; gap:22px;}
  .footer-links a{text-decoration:none; color:rgba(244,246,242,0.55);}
  .footer-links a:hover{color:var(--paper);}

  /* ============ RESPONSIVE ============ */
  @media (max-width:920px){
    .hero-grid{grid-template-columns:1fr;}
    .echo-wrap{height:280px; order:-1;}
    .loop-wrap{grid-template-columns:1fr;}
    .security-wrap{grid-template-columns:1fr; gap:34px;}
    .roles-grid{grid-template-columns:repeat(2,1fr);}
    .features-grid{grid-template-columns:1fr;}
    .proof-grid{grid-template-columns:1fr; gap:18px;}
    .proof-item{border-left:none; border-top:1px solid var(--line); padding-top:18px;}
    .proof-item:first-child{border-top:none; padding-top:0;}
  }
  @media (max-width:720px){
    .nav-links{display:none;}
  }
  @media (max-width:560px){
    .container{padding:0 20px;}
    section.block{padding:64px 0;}
    .roles-grid{grid-template-columns:1fr;}
  }
</style>
</head>
<body>

<header>
  <div class="nav">
    <a href="#" class="wordmark">Ecoa<span class="dot">.</span></a>
    <nav class="nav-links">
      <a href="#ciclo">Como funciona</a>
      <a href="#recursos">Recursos</a>
      <a href="#perfis">Para quem é</a>
      <a href="#seguranca">Segurança</a>
    </nav>
    <a href="/login" class="nav-cta">Entrar</a>
  </div>
</header>

<main>

  <!-- HERO -->
  <section class="hero">
    <div class="container hero-grid">
      <div>
        <span class="eyebrow hero-eyebrow">Acompanhamento fonoaudiológico infantil</span>
        <h1>A fala de cada criança, <em>acompanhada em rede.</em></h1>
        <p class="lede">
          O Ecoa conecta fonoaudiólogo, pediatra e terapeuta ocupacional em torno de um único
          plano terapêutico — com exercícios que a família reproduz em casa e uma evolução
          que fica registrada, não perdida em anotações soltas.
        </p>
        <div class="cta-row">
          <a href="/cadastro" class="btn btn-primary">Solicitar acesso</a>
          <a href="#ciclo" class="btn btn-ghost">Ver como funciona</a>
        </div>
      </div>

      <div class="echo-wrap" aria-hidden="true">
        <svg class="echo-rings" viewBox="0 0 400 400">
          <circle class="ring-a pulse-1" cx="200" cy="200" r="60"/>
          <circle class="ring-b pulse-2" cx="200" cy="200" r="60"/>
          <circle class="ring-c pulse-3" cx="200" cy="200" r="60"/>
          <circle class="echo-core" cx="200" cy="200" r="7"/>
          <text class="phoneme" x="248" y="130">/pa/</text>
          <text class="phoneme" x="90" y="150">/s/</text>
          <text class="phoneme" x="270" y="290">/ʁ/</text>
          <text class="phoneme" x="80" y="270">/l/</text>
        </svg>
      </div>
    </div>
  </section>

  <!-- PROOF STRIP -->
  <div class="proof">
    <div class="container proof-grid">
      <div class="proof-item">
        <div class="proof-num">1 plano</div>
        <div class="proof-label">por criança, comentado por todos os profissionais envolvidos</div>
      </div>
      <div class="proof-item">
        <div class="proof-num">Exercícios</div>
        <div class="proof-label">reaproveitados entre casos, não recriados a cada atendimento</div>
      </div>
      <div class="proof-item">
        <div class="proof-num">Dado sensível</div>
        <div class="proof-label">tratado com criptografia e controle de acesso por perfil</div>
      </div>
    </div>
  </div>

  <!-- CICLO -->
  <section class="block" id="ciclo">
    <div class="container loop-wrap">
      <div>
        <span class="eyebrow kicker">O ciclo do cuidado</span>
        <h2>Um plano vivo, não uma ficha parada na gaveta.</h2>
        <ul class="loop-list">
          <li>
            <span class="mark">01</span>
            <div>
              <h4>Avaliar</h4>
              <p>O fonoaudiólogo define fonemas-alvo, dificuldades e metas do plano terapêutico.</p>
            </div>
          </li>
          <li>
            <span class="mark">02</span>
            <div>
              <h4>Praticar</h4>
              <p>A família reproduz em casa os exercícios da biblioteca, já ajustados ao caso.</p>
            </div>
          </li>
          <li>
            <span class="mark">03</span>
            <div>
              <h4>Registrar</h4>
              <p>Sessões e exercícios em casa alimentam o diário de evolução da criança.</p>
            </div>
          </li>
          <li>
            <span class="mark">04</span>
            <div>
              <h4>Ajustar</h4>
              <p>Fonoaudiólogo, pediatra e terapeuta ocupacional revisam o plano juntos — e o ciclo recomeça.</p>
            </div>
          </li>
        </ul>
      </div>

      <svg class="loop-diagram" viewBox="-45 -15 510 450" aria-hidden="true">
        <circle cx="210" cy="210" r="150" fill="none" stroke="var(--line)" stroke-width="1.4"/>
        <path d="M 210 60 A 150 150 0 0 1 360 210" fill="none" stroke="#1b5e5a" stroke-width="2"/>
        <path d="M 360 210 A 150 150 0 0 1 210 360" fill="none" stroke="#c6873a" stroke-width="2"/>
        <path d="M 210 360 A 150 150 0 0 1 60 210" fill="none" stroke="#1b5e5a" stroke-width="2"/>
        <path d="M 60 210 A 150 150 0 0 1 210 60" fill="none" stroke="#c6873a" stroke-width="2"/>

        <circle cx="210" cy="60" r="5" fill="#1b5e5a"/>
        <circle cx="360" cy="210" r="5" fill="#c6873a"/>
        <circle cx="210" cy="360" r="5" fill="#1b5e5a"/>
        <circle cx="60" cy="210" r="5" fill="#c6873a"/>

        <text class="loop-step-label" x="210" y="38" text-anchor="middle">AVALIAR</text>
        <text class="loop-step-label" x="385" y="215" text-anchor="start">PRATICAR</text>
        <text class="loop-step-label" x="210" y="396" text-anchor="middle">REGISTRAR</text>
        <text class="loop-step-label" x="35" y="215" text-anchor="end">AJUSTAR</text>

        <text x="210" y="205" text-anchor="middle" font-family="Newsreader, serif" font-size="16" fill="#16231f">Plano</text>
        <text x="210" y="226" text-anchor="middle" font-family="Newsreader, serif" font-size="16" fill="#16231f">terapêutico</text>
      </svg>
    </div>
  </section>

  <!-- RECURSOS -->
  <section class="block" id="recursos" style="background:var(--paper-dim); border-top:1px solid var(--line); border-bottom:1px solid var(--line);">
    <div class="container">
      <span class="eyebrow kicker">Recursos essenciais</span>
      <h2>O que o Ecoa organiza no dia a dia da clínica.</h2>
      <p class="intro">
        Quatro peças que já existem, de forma dispersa, na rotina de qualquer equipe de
        fonoaudiologia infantil — aqui, conectadas em um único lugar.
      </p>

      <div class="features-grid">
        <div class="feature-card">
          <span class="feature-tag">Plano terapêutico</span>
          <h3>Plano colaborativo</h3>
          <p>Fonoaudiólogo, pediatra e terapeuta ocupacional comentam e ajustam o mesmo plano — sem decisão isolada de um só profissional.</p>
        </div>
        <div class="feature-card">
          <span class="feature-tag">Biblioteca</span>
          <h3>Exercícios de estimulação</h3>
          <p>Catalogados por fonema e dificuldade, com instruções claras para os pais reproduzirem em casa corretamente.</p>
        </div>
        <div class="feature-card">
          <span class="feature-tag">Acompanhamento</span>
          <h3>Diário de evolução</h3>
          <p>Progresso das sessões e dos exercícios em casa, relatado pelo profissional ou pela família, sempre com data e origem.</p>
        </div>
        <div class="feature-card">
          <span class="feature-tag">Gestão</span>
          <h3>Painel do coordenador</h3>
          <p>Visão consolidada de quantas crianças estão em acompanhamento e quais planos aguardam revisão.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- PERFIS -->
  <section class="block roles-band" id="perfis">
    <div class="container">
      <span class="eyebrow kicker">Acesso por perfil</span>
      <h2>Cada pessoa vê exatamente o que precisa.</h2>
      <p class="intro">Nada além disso — por princípio, não por limitação técnica.</p>

      <div class="roles-grid">
        <div class="role-card">
          <span class="role-eyebrow">Responsável pela terapia</span>
          <h4>Fonoaudiólogo</h4>
          <p>Cria e ajusta o plano, acessa a biblioteca e registra a evolução dos seus pacientes.</p>
        </div>
        <div class="role-card">
          <span class="role-eyebrow">Especialista colaborador</span>
          <h4>Pediatra / T.O.</h4>
          <p>Comenta e sugere ajustes no plano terapêutico dos casos que acompanha.</p>
        </div>
        <div class="role-card">
          <span class="role-eyebrow">Gestão clínica</span>
          <h4>Coordenador clínico</h4>
          <p>Leitura consolidada de planos e registros, com alertas de acompanhamento pendente.</p>
        </div>
        <div class="role-card">
          <span class="role-eyebrow">Família</span>
          <h4>Responsável legal</h4>
          <p>Acompanha exercícios e relatórios da criança — sem acesso a dados clínicos de terceiros.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- SEGURANÇA -->
  <section class="block security" id="seguranca">
    <div class="container security-wrap">
      <div>
        <span class="eyebrow kicker">Dado sensível, tratado como tal</span>
        <h2>Segurança pensada para dado de saúde infantil.</h2>
        <p class="intro" style="margin-bottom:0;">
          Informação sobre desenvolvimento da fala é dado pessoal sensível. O Ecoa foi desenhado
          para isso desde a primeira linha de código, não como reforço depois.
        </p>
      </div>
      <ul class="security-list">
        <li><span class="check">＋</span> Autenticação com verificação em duas etapas</li>
        <li><span class="check">＋</span> Senhas protegidas por hash com salt único por usuário</li>
        <li><span class="check">＋</span> Dados sensíveis criptografados em repouso e em trânsito</li>
        <li><span class="check">＋</span> Acesso restrito por perfil, por necessidade de atendimento</li>
        <li><span class="check">＋</span> Trilha de auditoria para toda alteração em registros clínicos</li>
      </ul>
    </div>
  </section>

  <!-- CLOSING -->
  <section class="closing">
    <div class="container">
      <span class="eyebrow" style="color:var(--sage);">Comece com uma turma piloto</span>
      <h2>Leve o Ecoa para a sua clínica ou setor de fonoaudiologia.</h2>
      <p>Sem substituir o trabalho clínico — só organizando o que já acontece entre profissionais, todos os dias.</p>
      <div class="cta-row">
        <a href="/cadastro" class="btn btn-primary">Solicitar acesso</a>
        <a href="mailto:contato@ecoa.app" class="btn btn-ghost">Falar com a equipe</a>
      </div>
    </div>
  </section>

</main>

<footer>
  <div class="container footer-row">
    <span>© 2026 Ecoa — Sistema de Acompanhamento Fonoaudiológico Infantil</span>
    <div class="footer-links">
      <a href="#ciclo">Como funciona</a>
      <a href="#seguranca">Segurança</a>
      <a href="/login">Entrar</a>
    </div>
  </div>
</footer>

</body>
</html>