@php
    // Calculado uma única vez. Funciona com ou sem variáveis vindas da rota.
    $usuario = auth()->user();
    $ultimoConsentimento = $usuario->consentimentos()->latest('aceito_em')->first();
    $consentimentoAtivo = $usuario->consentimentos()->whereNull('revogado_em')->latest('aceito_em')->first();

    // ativo | revogado | pendente (nunca registrou consentimento)
    $estadoConsentimento = $consentimentoAtivo ? 'ativo' : ($ultimoConsentimento ? 'revogado' : 'pendente');
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel — Ecoa</title>

    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Newsreader:opsz,wght@6..72,400;6..72,500;6..72,600&family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap"
        rel="stylesheet">

    {{-- CSS global do Ecoa (antes vivia inline em cada página; agora
         compilado via Vite e compartilhado entre todas as telas). --}}
    @vite(['resources/css/app.css'])
</head>
<body>

    {{-- ========================================================= HEADER ========================================================== --}}
    <header>
        <div class="container">
            <div class="nav">

                <a href="{{ route('landing') }}" class="wordmark" aria-label="Ecoa — Página inicial">
                    <img src="{{ asset('img/ecoa-icone.png') }}" alt="Ecoa">
                </a>

                <div class="nav-right">

                    <a href="{{ route('security.index') }}" class="security-link" title="Configurações de segurança">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="4" y="11" width="16" height="9" rx="2"></rect>
                            <path d="M8 11V8a4 4 0 0 1 8 0v3"></path>
                            <circle cx="12" cy="15" r="1"></circle>
                        </svg>
                        <span>Segurança</span>
                    </a>

                    <div class="account" id="accountMenu">
                        <button type="button" class="account-button" id="accountButton" aria-expanded="false" aria-haspopup="true">
                            <span class="account-avatar">{{ strtoupper(substr($usuario->name, 0, 1)) }}</span>
                            <span class="account-info">
                                <span class="account-name">{{ $usuario->name }}</span>
                                <span class="account-role">{{ ucwords(str_replace('_', ' ', $usuario->perfil ?? 'Usuário')) }}</span>
                            </span>
                            <svg class="account-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="m6 9 6 6 6-6"></path>
                            </svg>
                        </button>

                        <div class="account-dropdown" role="menu">
                            <div class="dropdown-user">
                                <span class="dropdown-avatar">{{ strtoupper(substr($usuario->name, 0, 1)) }}</span>
                                <div>
                                    <strong>{{ $usuario->name }}</strong>
                                    <span>{{ $usuario->email }}</span>
                                </div>
                            </div>

                            <a href="{{ route('security.index') }}" class="dropdown-link" role="menuitem">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 3 5 6v5c0 4.5 3 7.5 7 10 4-2.5 7-5.5 7-10V6l-7-3Z"></path>
                                    <path d="m9 12 2 2 4-4"></path>
                                </svg>
                                Segurança da conta
                            </a>

                            <a href="{{ route('lgpd.meus-dados') }}" class="dropdown-link" role="menuitem">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="9"></circle>
                                    <path d="M12 11v5"></path>
                                    <path d="M12 8h.01"></path>
                                </svg>
                                Meus dados e privacidade
                            </a>

                            {{-- Ferramentas de administração: só aparecem para quem tem permissão --}}
                            @canany(['gerenciar-acessos', 'ver-auditoria'])
                                <div class="dropdown-divider"></div>

                                @can('gerenciar-acessos')
                                    <a href="{{ route('convites.index') }}" class="dropdown-link" role="menuitem">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="9" cy="8" r="3"></circle>
                                            <path d="M3.5 19a5.5 5.5 0 0 1 11 0"></path>
                                            <path d="M19 8v6"></path>
                                            <path d="M16 11h6"></path>
                                        </svg>
                                        Acessos profissionais
                                    </a>
                                @endcan

                                @can('ver-auditoria')
                                    <a href="{{ route('auditoria.index') }}" class="dropdown-link" role="menuitem">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"></path>
                                            <path d="M14 2v6h6"></path>
                                            <path d="M8 13h8"></path>
                                            <path d="M8 17h5"></path>
                                        </svg>
                                        Auditoria e logs
                                    </a>
                                @endcan
                            @endcanany

                            <div class="dropdown-divider"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-logout" role="menuitem">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                        <path d="m16 17 5-5-5-5"></path>
                                        <path d="M21 12H9"></path>
                                    </svg>
                                    Sair da conta
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- ========================================================= HERO ========================================================== --}}
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <span class="eyebrow">Painel Ecoa</span>
                <h1>Olá, {{ explode(' ', $usuario->name)[0] }}.</h1>
                <p>Seu espaço para acompanhar atendimentos, organizar informações e manter o cuidado infantil reunido em um só lugar.</p>

                <div class="hero-meta">
                    <span class="hero-meta-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="M12 7v5l3 2"></path>
                        </svg>
                        Ambiente de acompanhamento
                    </span>
                    <span class="hero-meta-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 7 10 17l-5-5"></path>
                        </svg>
                        Sessão protegida
                    </span>
                </div>

                @unless ($usuario->two_factor_confirmed_at)
                    <div class="banner">
                        <div class="banner-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 3 5 6v5c0 4.5 3 7.5 7 10 4-2.5 7-5.5 7-10V6l-7-3Z"></path>
                                <path d="M12 8v4"></path>
                                <path d="M12 15h.01"></path>
                            </svg>
                        </div>
                        <div class="banner-content">
                            <p>Sua conta ainda não possui a verificação em duas etapas.</p>
                            <a href="{{ route('security.index') }}">Ativar agora &rarr;</a>
                        </div>
                    </div>
                @endunless

                {{-- Aviso de consentimento: a home abre normalmente, mas o usuário é avisado --}}
                @if ($estadoConsentimento !== 'ativo')
                    <div class="banner">
                        <div class="banner-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="M12 8v4"></path>
                                <path d="M12 15h.01"></path>
                            </svg>
                        </div>
                        <div class="banner-content">
                            <p>
                                @if ($estadoConsentimento === 'revogado')
                                    Seu consentimento foi revogado. Algumas áreas do sistema estão bloqueadas.
                                @else
                                    Sua conta ainda não tem um consentimento registrado. Algumas áreas do sistema estão bloqueadas.
                                @endif
                            </p>
                            <a href="{{ route('lgpd.meus-dados') }}">Resolver agora &rarr;</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- ========================================================= DASHBOARD ========================================================== --}}
    <main class="dashboard">
        <div class="container">

            <div class="section-heading">
                <div>
                    <h2>Visão geral</h2>
                    <p>Um resumo rápido do seu ambiente de acompanhamento.</p>
                </div>
                <span class="section-note">Atualizado agora</span>
            </div>

            {{-- CARDS --}}
            <div class="cards">
                <div class="card">
                    <div class="card-top">
                        <h3>Crianças</h3>
                        <div class="card-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="9" cy="8" r="3"></circle>
                                <path d="M3.5 19a5.5 5.5 0 0 1 11 0"></path>
                                <path d="M16 5.5a3 3 0 0 1 0 5.8"></path>
                                <path d="M17 14a5 5 0 0 1 4 5"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="value">0</div>
                    <div class="hint">nenhum acompanhamento cadastrado</div>
                </div>

                <div class="card">
                    <div class="card-top">
                        <h3>Planos terapêuticos</h3>
                        <div class="card-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"></path>
                                <path d="M14 2v6h6"></path>
                                <path d="M8 13h8"></path>
                                <path d="M8 17h5"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="value">0</div>
                    <div class="hint">nenhum plano criado</div>
                </div>

                <div class="card">
                    <div class="card-top">
                        <h3>Exercícios</h3>
                        <div class="card-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 7v10"></path><path d="M8 5v14"></path>
                                <path d="M16 5v14"></path><path d="M20 7v10"></path>
                                <path d="M2 9h6"></path><path d="M16 9h6"></path>
                                <path d="M2 15h6"></path><path d="M16 15h6"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="value">0</div>
                    <div class="hint">biblioteca ainda vazia</div>
                </div>

                <div class="card">
                    <div class="card-top">
                        <h3>Profissionais</h3>
                        <div class="card-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="7" r="3"></circle>
                                <path d="M5 21a7 7 0 0 1 14 0"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="value">1</div>
                    <div class="hint">você</div>
                </div>
            </div>

            {{-- ADMINISTRAÇÃO: atalhos de acesso rápido para quem tem permissão --}}
            @canany(['gerenciar-acessos', 'ver-auditoria'])
                <section class="quick-actions" style="margin-top:0; margin-bottom:38px;">
                    <div class="quick-header">
                        <div>
                            <h2>Administração</h2>
                            <p>Ferramentas disponíveis para o seu perfil.</p>
                        </div>
                        <span class="quick-label">Acesso restrito</span>
                    </div>

                    <div class="quick-grid">
                        @can('gerenciar-acessos')
                            <a href="{{ route('convites.index') }}" class="quick-action">
                                <span class="quick-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="9" cy="8" r="3"></circle>
                                        <path d="M3.5 19a5.5 5.5 0 0 1 11 0"></path>
                                        <path d="M19 8v6"></path>
                                        <path d="M16 11h6"></path>
                                    </svg>
                                </span>
                                <span class="quick-text">
                                    <strong>Acessos profissionais</strong>
                                    <span>Convidar e gerenciar profissionais</span>
                                </span>
                                <span class="quick-arrow">&rarr;</span>
                            </a>
                        @endcan

                        @can('ver-auditoria')
                            <a href="{{ route('auditoria.index') }}" class="quick-action">
                                <span class="quick-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 3 5 6v5c0 4.5 3 7.5 7 10 4-2.5 7-5.5 7-10V6l-7-3Z"></path>
                                        <path d="m9 12 2 2 4-4"></path>
                                    </svg>
                                </span>
                                <span class="quick-text">
                                    <strong>Auditoria e logs</strong>
                                    <span>Eventos de segurança e integridade</span>
                                </span>
                                <span class="quick-arrow">&rarr;</span>
                            </a>
                        @endcan
                    </div>
                </section>
            @endcanany

            {{-- PAINÉIS --}}
            <div class="panels">

                <div class="panel">
                    <div class="panel-header">
                        <h2>Seu perfil</h2>
                        <span class="panel-label">Sessão ativa</span>
                    </div>
                    <div class="profile-info">
                        <div class="profile-item">
                            <span>Nome</span>
                            <strong>{{ $usuario->name }}</strong>
                        </div>
                        <div class="profile-item">
                            <span>E-mail</span>
                            <strong>{{ $usuario->email }}</strong>
                        </div>
                        <div class="profile-item">
                            <span>Perfil de acesso</span>
                            <strong>{{ ucwords(str_replace('_', ' ', $usuario->perfil ?? 'Não definido')) }}</strong>
                        </div>
                        <div class="profile-item">
                            <span>Membro desde</span>
                            <strong>{{ \App\Support\FusoHorario::dataPorExtenso($usuario->created_at, 'Não informado') }}</strong>
                        </div>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-header">
                        <h2>Segurança</h2>
                    </div>

                    @if ($usuario->two_factor_confirmed_at)
                        <div class="security-status on">
                            <span class="status-dot on"></span>
                            Verificação em duas etapas ativada
                        </div>
                        <p class="security-desc">Sua conta está protegida por um segundo fator de autenticação.</p>
                        <div class="security-details">
                            <div class="security-detail"><span>Status</span><strong>Protegida</strong></div>
                            <div class="security-detail"><span>2FA</span><strong>Ativo</strong></div>
                        </div>
                    @else
                        <div class="security-status off">
                            <span class="status-dot off"></span>
                            Verificação em duas etapas desativada
                        </div>
                        <p class="security-desc">Adicione uma camada extra de proteção à sua conta usando um aplicativo autenticador.</p>
                        <div class="security-details">
                            <div class="security-detail"><span>Status</span><strong>Atenção necessária</strong></div>
                            <div class="security-detail"><span>2FA</span><strong>Inativo</strong></div>
                        </div>
                    @endif

                    <a href="{{ route('security.index') }}" class="btn-outline">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 3 5 6v5c0 4.5 3 7.5 7 10 4-2.5 7-5.5 7-10V6l-7-3Z"></path>
                            <path d="m9 12 2 2 4-4"></path>
                        </svg>
                        Gerenciar segurança
                    </a>
                </div>
            </div>

            {{-- PRIVACIDADE E LGPD --}}
            <section class="privacy-panel">
                <div class="privacy-header">
                    <div class="privacy-title">
                        <h2>Privacidade e LGPD</h2>
                        <p>Consulte e gerencie os dados pessoais associados à sua conta.</p>
                    </div>
                    <span class="privacy-label">Direitos do titular</span>
                </div>

                <div class="privacy-content">
                    <div class="privacy-status">
                        <div class="privacy-status-top">
                            @if ($estadoConsentimento === 'ativo')
                                <span class="privacy-dot"></span>
                                <strong>Seu consentimento está ativo</strong>
                            @elseif ($estadoConsentimento === 'revogado')
                                <span class="privacy-dot danger"></span>
                                <strong>Seu consentimento foi revogado</strong>
                            @else
                                <span class="privacy-dot warning"></span>
                                <strong>Nenhum consentimento registrado</strong>
                            @endif
                        </div>

                        @if ($estadoConsentimento === 'ativo')
                            <p>Você pode consultar seus dados, exportar uma cópia, revogar o consentimento ou solicitar a exclusão a qualquer momento.</p>
                        @else
                            <p>Sem consentimento ativo, as áreas que tratam dados, como o painel de auditoria, ficam bloqueadas. Conceda o consentimento em “Meus dados” para liberá-las.</p>
                        @endif
                    </div>

                    <div class="privacy-meta">
                        <div class="privacy-meta-item">
                            <span>Situação</span>
                            <strong>
                                @if ($estadoConsentimento === 'ativo') Ativo
                                @elseif ($estadoConsentimento === 'revogado') Revogado
                                @else Não registrado
                                @endif
                            </strong>
                        </div>
                        <div class="privacy-meta-item">
                            <span>Versão do termo</span>
                            <strong>{{ $ultimoConsentimento?->versao_termo ?? '—' }}</strong>
                        </div>
                        <div class="privacy-meta-item">
                            <span>Finalidade</span>
                            <strong>{{ $ultimoConsentimento?->finalidade ?? '—' }}</strong>
                        </div>
                        <div class="privacy-meta-item">
                            <span>{{ $estadoConsentimento === 'revogado' ? 'Revogado em' : 'Concedido em' }}</span>
                            <strong>
                                @if ($estadoConsentimento === 'revogado')
                                    {{ \App\Support\FusoHorario::formatar($ultimoConsentimento->revogado_em) }}
                                @else
                                    {{ \App\Support\FusoHorario::formatar($ultimoConsentimento?->aceito_em) }}
                                @endif
                            </strong>
                        </div>
                    </div>
                </div>

                <div class="privacy-actions">
                    <a href="{{ route('lgpd.meus-dados') }}" class="privacy-button primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="M12 11v5"></path>
                            <path d="M12 8h.01"></path>
                        </svg>
                        Meus dados e privacidade
                    </a>
                    <a href="{{ route('lgpd.exportar') }}" class="privacy-button secondary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 3v12"></path>
                            <path d="m7 10 5 5 5-5"></path>
                            <path d="M5 21h14"></path>
                        </svg>
                        Exportar meus dados
                    </a>
                </div>
            </section>

            {{-- ACESSO RÁPIDO --}}
            <section class="quick-actions">
                <div class="quick-header">
                    <div>
                        <h2>Acesso rápido</h2>
                        <p>Atalhos para as principais áreas do Ecoa.</p>
                    </div>
                    <span class="quick-label">Próximas ações</span>
                </div>

                <div class="quick-grid">
                    {{-- Módulos de Criança, Plano Terapêutico e Exercícios ainda
                         não têm tela própria — em vez de link morto (href="#"),
                         ficam com rótulo "Em breve" até existirem rotas reais. --}}
                    <div class="quick-action is-upcoming" aria-disabled="true">
                        <span class="quick-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="9" cy="8" r="3"></circle>
                                <path d="M3.5 19a5.5 5.5 0 0 1 11 0"></path>
                                <path d="M19 8v6"></path>
                                <path d="M16 11h6"></path>
                            </svg>
                        </span>
                        <span class="quick-text">
                            <strong>Nova criança</strong>
                            <span>Iniciar acompanhamento</span>
                        </span>
                        <span class="quick-soon">Em breve</span>
                    </div>

                    <div class="quick-action is-upcoming" aria-disabled="true">
                        <span class="quick-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"></path>
                                <path d="M14 2v6h6"></path>
                                <path d="M12 12v6"></path>
                                <path d="M9 15h6"></path>
                            </svg>
                        </span>
                        <span class="quick-text">
                            <strong>Novo plano</strong>
                            <span>Criar plano terapêutico</span>
                        </span>
                        <span class="quick-soon">Em breve</span>
                    </div>

                    <div class="quick-action is-upcoming" aria-disabled="true">
                        <span class="quick-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 7v10"></path><path d="M8 5v14"></path>
                                <path d="M16 5v14"></path><path d="M20 7v10"></path>
                                <path d="M2 9h6"></path><path d="M16 9h6"></path>
                                <path d="M2 15h6"></path><path d="M16 15h6"></path>
                            </svg>
                        </span>
                        <span class="quick-text">
                            <strong>Exercícios</strong>
                            <span>Consultar biblioteca</span>
                        </span>
                        <span class="quick-soon">Em breve</span>
                    </div>
                </div>
            </section>

        </div>
    </main>

    {{-- ========================================================= FOOTER ========================================================== --}}
    <footer>
        <div class="container">
            <div class="footer-row">
                <div class="footer-brand">
                    <img src="{{ asset('img/ecoa-icone.png') }}" alt="">
                    <span>© {{ date('Y') }} Ecoa — Sistema de Acompanhamento Fonoaudiológico Infantil</span>
                </div>
                <div class="footer-links">
                    <a href="{{ route('landing') }}">Início</a>
                    <a href="{{ route('home') }}">Painel</a>
                    <a href="{{ route('security.index') }}">Segurança</a>
                    <a href="{{ route('lgpd.meus-dados') }}">Privacidade</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        const accountMenu = document.getElementById('accountMenu');
        const accountButton = document.getElementById('accountButton');

        accountButton.addEventListener('click', function (event) {
            event.stopPropagation();
            const isOpen = accountMenu.classList.toggle('open');
            accountButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        document.addEventListener('click', function (event) {
            if (!accountMenu.contains(event.target)) {
                accountMenu.classList.remove('open');
                accountButton.setAttribute('aria-expanded', 'false');
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                accountMenu.classList.remove('open');
                accountButton.setAttribute('aria-expanded', 'false');
                accountButton.focus();
            }
        });
    </script>

</body>
</html>
