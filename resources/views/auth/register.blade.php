{{--
    Cadastro de novo usuário. Valida e cria via
    app/Actions/Fortify/CreateNewUser.php — hash Argon2id na senha e
    registro de consentimento LGPD (tabela "consentimentos").

    CONSENTIMENTO COM LEITURA OBRIGATÓRIA
    O checkbox de aceite nasce DESABILITADO. Ele só é liberado quando a
    pessoa abre os Termos de Uso e rola o texto até o final e clica em
    "Li e aceito". Camadas de proteção:
      1. Interface: checkbox desabilitado + botão de aceite bloqueado
         até o fim da rolagem (IntersectionObserver).
      2. Envio do formulário: se o checkbox ainda estiver desabilitado,
         o submit é cancelado e os termos são abertos.
      3. Servidor: CreateNewUser exige 'aceite_lgpd' => required|accepted,
         então nenhum cadastro é criado sem aceite, mesmo que alguém
         desative o JavaScript.
    O aceite nunca vem pré-marcado: o clique em "Li e aceito" é a ação
    afirmativa do usuário, e fica registrado com data, versão e IP.
--}}
@php
    // Versão do texto exibido abaixo. Vem de config/ecoa.php, a mesma fonte
    // usada ao gravar o consentimento (CreateNewUser). Ao alterar o texto dos
    // termos, altere a versão lá.
    $versaoTermo = config('ecoa.termos_versao', 'v1.0');

    // $convite é entregue pelo View Composer registrado no AppServiceProvider:
    // é o convite válido do link (?convite=...) ou null no cadastro aberto.
    $convite = $convite ?? null;
    $tokenInformado = request('convite');
@endphp
<x-layouts.guest :title="'Criar conta — Ecoa'" :subtitle="'Crie sua conta de acesso'">

    @if ($errors->any())
        <div class="auth-alert error">
            @foreach ($errors->all() as $error)
                {{ $error }}
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" id="registerForm">
        @csrf

        {{-- O PERFIL NÃO É ESCOLHIDO AQUI. Cadastro aberto cria "Responsável Legal";
             conta profissional só nasce de um convite emitido por um administrador. --}}
        @if ($convite)
            <input type="hidden" name="convite" value="{{ $tokenInformado }}">
            <div class="auth-alert success">
                Você foi convidado(a) para criar uma conta de <strong>{{ $convite->perfil->rotulo() }}</strong>.
                Use o e-mail do convite abaixo.
            </div>
        @elseif ($tokenInformado)
            <div class="auth-alert error">
                Este convite é inválido ou expirou. Peça um novo à administração do Ecoa.
                O cadastro abaixo criaria uma conta de <strong>Responsável Legal</strong>.
            </div>
        @else
            <div class="auth-form-text" style="margin-bottom:16px;">
                Este cadastro cria uma conta de <strong>Responsável Legal</strong>.
                Contas de profissionais são criadas por convite da administração.
            </div>
        @endif

        <div class="auth-form-group">
            <label for="name">Nome completo</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}"
                   required autofocus autocomplete="name">
        </div>

        <div class="auth-form-group">
            <label for="email">E-mail</label>
            <input id="email" type="email" name="email" value="{{ $convite ? $convite->email : old('email') }}"
                   {{ $convite ? 'readonly' : '' }} required autocomplete="username">
        </div>

        <div class="auth-form-group">
            <label for="password">Senha</label>
            <input id="password" type="password" name="password" required autocomplete="new-password">
            <div class="auth-form-text">Mínimo de 8 caracteres.</div>
        </div>

        <div class="auth-form-group">
            <label for="password_confirmation">Confirme a senha</label>
            <input id="password_confirmation" type="password" name="password_confirmation"
                   required autocomplete="new-password">
        </div>

        {{-- CONSENTIMENTO LGPD --}}
        <div class="auth-form-group">
            <div class="consent-row">
                <input type="checkbox" name="aceite_lgpd" id="aceite_lgpd" required disabled
                       aria-describedby="consentHint">
                <label for="aceite_lgpd">
                    Li e aceito os
                    <button type="button" class="terms-link-btn" id="openTerms">Termos de Uso e a Política de Privacidade</button>
                    do Ecoa (versão {{ $versaoTermo }})
                </label>
            </div>
            <div class="consent-hint" id="consentHint">Abra e leia os termos até o final para habilitar esta opção.</div>
        </div>

        <button type="submit" class="btn-ecoa">Criar conta</button>

        <div style="text-align:center; margin-top:16px;">
            <a href="{{ route('login') }}" class="auth-link">Já tem conta? Entrar</a>
        </div>
    </form>

    {{-- ===================== TERMOS DE USO E POLÍTICA DE PRIVACIDADE ===================== --}}
    <dialog class="tos-dialog" id="tosDialog" aria-labelledby="tosTitle">

        <div class="tos-progress" role="progressbar" aria-label="Progresso da leitura dos termos"
             aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
            <div class="tos-progress-bar" id="tosProgress"></div>
        </div>

        <div class="tos-header">
            <div>
                <h3 id="tosTitle">Termos de Uso e Política de Privacidade</h3>
                <span class="tos-meta">Ecoa · versão {{ $versaoTermo }} · outubro de 2026</span>
            </div>
            <button type="button" class="tos-close" id="tosClose" aria-label="Fechar os termos">&times;</button>
        </div>

        <div class="tos-body" id="tosBody" tabindex="0">

            <div class="tos-summary">
                <strong>Resumo em linguagem simples</strong>
                <ul>
                    <li>O Ecoa ajuda profissionais e famílias a acompanharem a terapia de fala de crianças em um só lugar.</li>
                    <li>Os dados de saúde de crianças são <strong>dados sensíveis</strong>. Coletamos só o necessário e os protegemos.</li>
                    <li>Você pode consultar, exportar, revogar o consentimento e excluir seus dados dentro do próprio sistema.</li>
                    <li>O Ecoa é um <strong>projeto acadêmico</strong> e não substitui o julgamento clínico do profissional.</li>
                </ul>
                <p style="margin:10px 0 0;">Este resumo não substitui o texto completo abaixo.</p>
            </div>

            <h4>1. Quem somos e o que é o Ecoa</h4>
            <p>O Ecoa é uma plataforma web de apoio ao acompanhamento fonoaudiológico infantil. Ele conecta o
            fonoaudiólogo, outros profissionais envolvidos no caso (como pediatra e terapeuta ocupacional), a
            coordenação clínica e o responsável legal da criança em torno do plano terapêutico, dos exercícios
            de estimulação da fala e do registro da evolução.</p>
            <p>O sistema foi desenvolvido como projeto acadêmico do curso de Sistemas de Informação da
            Universidade de Mogi das Cruzes. Ele organiza informações e facilita a comunicação, mas <strong>não
            realiza diagnóstico nem substitui a avaliação e a conduta clínica do profissional de saúde</strong>.</p>

            <h4>2. Definições</h4>
            <ul>
                <li><strong>Titular:</strong> a pessoa a quem os dados se referem (você e, no caso da criança, a própria criança, representada pelo responsável legal).</li>
                <li><strong>Dado pessoal:</strong> informação que identifica ou pode identificar uma pessoa.</li>
                <li><strong>Dado pessoal sensível:</strong> inclui dado referente à saúde. As informações do plano terapêutico e da evolução da criança se enquadram aqui.</li>
                <li><strong>Responsável legal:</strong> pai, mãe ou representante legal da criança.</li>
                <li><strong>LGPD:</strong> Lei Geral de Proteção de Dados (Lei nº 13.709/2018).</li>
            </ul>

            <h4>3. Cadastro, conta e responsabilidades</h4>
            <ul>
                <li>Você deve informar dados verdadeiros. O perfil de acesso é definido pelo Ecoa: o cadastro aberto cria contas de Responsável Legal, e contas de profissionais são criadas por convite da administração.</li>
                <li>A senha é pessoal e intransferível. Você é responsável pelo sigilo dela e por tudo que for feito com a sua conta.</li>
                <li>Contas de perfil profissional (fonoaudiólogo, coordenador clínico e administrador de TI) devem ativar a <strong>verificação em duas etapas (2FA)</strong> para acessar o sistema.</li>
                <li>Profissionais se comprometem a acessar apenas as informações necessárias ao atendimento das crianças sob seus cuidados.</li>
                <li>O responsável legal declara ter autoridade para fornecer os dados da criança e consentir com o tratamento deles.</li>
            </ul>

            <h4>4. Uso aceitável</h4>
            <p>Não é permitido: tentar burlar mecanismos de segurança; acessar dados de crianças ou de outros
            usuários sem necessidade de atendimento; inserir conteúdo ilícito ou que não tenha relação com o
            acompanhamento terapêutico; ou usar o sistema para finalidade diferente da prevista nestes termos.</p>

            <h4>5. Quais dados coletamos e para quê</h4>
            <div class="tos-table-wrap">
                <table class="tos-table">
                    <thead>
                        <tr><th>Dado</th><th>Finalidade</th><th>Base legal</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Nome, e-mail, senha (armazenada apenas como hash) e perfil de acesso</td>
                            <td>Autenticação e controle de permissões</td>
                            <td>Consentimento; execução do serviço</td>
                        </tr>
                        <tr>
                            <td>Nome, contato e e-mail do responsável legal</td>
                            <td>Vínculo com a criança e comunicação sobre o atendimento</td>
                            <td>Consentimento</td>
                        </tr>
                        <tr>
                            <td>Nome e data de nascimento da criança</td>
                            <td>Identificação do paciente acompanhado</td>
                            <td>Consentimento do responsável legal (art. 14)</td>
                        </tr>
                        <tr>
                            <td>Tipo de dificuldade, fonemas-alvo, metas, comentários e observações de evolução (dados de saúde)</td>
                            <td>Acompanhamento fonoaudiológico</td>
                            <td>Consentimento e tutela da saúde (art. 11)</td>
                        </tr>
                        <tr>
                            <td>Endereço IP, data e hora de acessos, falhas de login e registros de consentimento</td>
                            <td>Segurança, auditoria e comprovação do consentimento</td>
                            <td>Consentimento; legítimo interesse em segurança</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p><strong>O que não coletamos:</strong> diagnóstico médico completo, histórico geral de saúde,
            documentos de identificação (como CPF e RG) e dados de pagamento. Seguimos o princípio da
            minimização: só tratamos o necessário para a finalidade terapêutica.</p>

            <h4>6. Base legal e consentimento</h4>
            <p>O tratamento dos dados ocorre com base no seu consentimento, dado ao aceitar este documento, e,
            para dados de saúde, também na tutela da saúde, em procedimento realizado por profissionais de saúde
            (LGPD, arts. 7º e 11). Para dados de crianças, o consentimento é dado de forma específica e em
            destaque por ao menos um dos pais ou pelo responsável legal (art. 14).</p>
            <p>Registramos o seu aceite com data, hora, versão deste texto e endereço IP, para comprovar que o
            consentimento foi dado. Você pode revogá-lo a qualquer momento, de forma simples e gratuita.</p>

            <h4>7. Dados de crianças</h4>
            <p>O tratamento de dados de crianças é feito no melhor interesse delas e exclusivamente para o
            acompanhamento terapêutico. As informações clínicas são tratadas como sensíveis e protegidas com
            os controles descritos na seção de segurança.</p>

            <h4>8. Compartilhamento</h4>
            <ul>
                <li>As informações clínicas de uma criança ficam disponíveis aos profissionais envolvidos no atendimento e à coordenação clínica, seguindo o princípio de que cada pessoa acessa só o que precisa.</li>
                <li>Não vendemos dados e não os compartilhamos com terceiros para publicidade.</li>
                <li>Usamos provedores de hospedagem, banco de dados e envio de e-mail apenas como prestadores que viabilizam o funcionamento técnico do sistema.</li>
                <li>Dados podem ser fornecidos a autoridades quando houver obrigação legal ou ordem judicial.</li>
            </ul>

            <h4>9. Segurança da informação</h4>
            <ul>
                <li>Senhas protegidas com hash Argon2id, com salt único por usuário. Nunca guardamos a senha em texto puro.</li>
                <li>Verificação em duas etapas (2FA) por aplicativo autenticador, obrigatória para perfis profissionais.</li>
                <li>Dados clínicos sensíveis criptografados em repouso (AES-256).</li>
                <li>Conexão protegida por HTTPS em todo o sistema.</li>
                <li>Limite de tentativas de login e de 2FA, e sessões com expiração.</li>
                <li>Trilha de auditoria de eventos de segurança, com verificação de integridade contra adulteração.</li>
            </ul>
            <p>Nenhum sistema é totalmente imune a falhas. Em caso de incidente de segurança que possa causar
            risco relevante, comunicaremos os titulares e a Autoridade Nacional de Proteção de Dados (ANPD),
            conforme a lei.</p>

            <h4>10. Por quanto tempo guardamos os dados</h4>
            <p>Mantemos os dados enquanto a sua conta estiver ativa e eles forem necessários para a finalidade
            do acompanhamento. Quando você solicita a exclusão, os dados identificáveis da conta são
            anonimizados e a conta é desativada. Registros de consentimento e de auditoria podem ser preservados
            pelo mínimo necessário para comprovar o consentimento e garantir a segurança do sistema.</p>

            <h4>11. Seus direitos como titular</h4>
            <p>Conforme o art. 18 da LGPD, você tem direito à confirmação do tratamento, ao acesso, à correção,
            à portabilidade, à eliminação dos dados tratados com consentimento, à informação sobre
            compartilhamento e à revogação do consentimento. No próprio sistema, em <strong>Meus dados</strong>,
            você pode:</p>
            <ul>
                <li>consultar os dados da sua conta e o histórico de consentimentos;</li>
                <li>exportar os seus dados em formato legível (JSON);</li>
                <li>revogar o consentimento;</li>
                <li>solicitar a exclusão (anonimização) dos seus dados pessoais.</li>
            </ul>
            <p>Para os demais direitos, como correção de dados, escreva para o contato indicado abaixo.</p>

            <h4>12. Cookies e serviços externos</h4>
            <p>Usamos apenas cookies essenciais ao funcionamento: o de sessão (mantém você autenticado) e o de
            proteção contra requisições forjadas (CSRF). Não usamos cookies de publicidade nem de rastreamento
            de comportamento. As páginas carregam fontes tipográficas de um serviço externo (Google Fonts), o
            que pode expor o seu endereço IP a esse serviço.</p>

            <h4>13. Alterações destes termos</h4>
            <p>Este texto pode ser atualizado. Cada versão é identificada (esta é a {{ $versaoTermo }}). Mudanças
            relevantes na finalidade do tratamento exigirão um novo aceite, e o histórico dos seus
            consentimentos fica disponível em <strong>Meus dados</strong>.</p>

            <h4>14. Limitação de responsabilidade</h4>
            <p>Por ser um projeto acadêmico, o Ecoa é oferecido no estado em que se encontra, sem garantia de
            disponibilidade ininterrupta. As informações registradas são de responsabilidade de quem as insere,
            e as decisões clínicas continuam sendo exclusivamente do profissional de saúde responsável.</p>

            <h4>15. Contato e encarregado de dados</h4>
            <p>Dúvidas, solicitações de titulares e comunicações sobre privacidade podem ser enviadas para
            <strong>fonoaudiologiaecoa@gmail.com</strong>. Este documento é regido pela legislação brasileira,
            em especial pela Lei nº 13.709/2018 (LGPD).</p>

            <div class="tos-end" id="tosEnd"></div>
            <p class="tos-end-label">Fim dos termos</p>
        </div>

        <div class="tos-footer">
            <div class="tos-scroll-hint" id="tosScrollHint">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
                Role até o final do texto para habilitar o aceite
            </div>
            <button type="button" class="tos-accept" id="tosAccept" disabled>Li e aceito os termos</button>
        </div>

        {{-- Aviso para leitores de tela quando o aceite é liberado --}}
        <div class="tos-live" id="tosLive" aria-live="polite"></div>
    </dialog>

    <script>
        (function () {
            const STORAGE_KEY = 'ecoa_termos_lidos_{{ $versaoTermo }}';

            const form = document.getElementById('registerForm');
            const checkbox = document.getElementById('aceite_lgpd');
            const hint = document.getElementById('consentHint');
            const openBtn = document.getElementById('openTerms');
            const dialog = document.getElementById('tosDialog');
            const closeBtn = document.getElementById('tosClose');
            const body = document.getElementById('tosBody');
            const endMarker = document.getElementById('tosEnd');
            const bar = document.getElementById('tosProgress');
            const acceptBtn = document.getElementById('tosAccept');
            const scrollHint = document.getElementById('tosScrollHint');
            const live = document.getElementById('tosLive');

            let reachedEnd = false;

            function unlockAccept() {
                if (reachedEnd) return;
                reachedEnd = true;
                acceptBtn.disabled = false;
                scrollHint.hidden = true;
                live.textContent = 'Leitura concluída. O botão de aceite foi habilitado.';
            }

            // Avisa quando o marcador do fim do texto fica visível dentro da
            // área de rolagem. Mais confiável que comparar scrollTop, que
            // varia com zoom e arredondamento.
            new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) unlockAccept();
                });
            }, { root: body, threshold: 1.0 }).observe(endMarker);

            body.addEventListener('scroll', function () {
                const max = body.scrollHeight - body.clientHeight;
                const pct = max > 0 ? Math.min(100, Math.round((body.scrollTop / max) * 100)) : 100;
                bar.style.width = pct + '%';
                bar.parentElement.setAttribute('aria-valuenow', pct);
            }, { passive: true });

            function openTerms() {
                dialog.showModal();
                body.focus(); // permite rolar com o teclado (setas, Page Down)
            }

            openBtn.addEventListener('click', openTerms);
            closeBtn.addEventListener('click', function () { dialog.close(); });

            // Clique no fundo escuro fecha a janela
            dialog.addEventListener('click', function (e) {
                if (e.target === dialog) dialog.close();
            });

            acceptBtn.addEventListener('click', function () {
                checkbox.disabled = false;
                checkbox.checked = true; // ação afirmativa do usuário
                hint.textContent = 'Termos lidos e aceitos.';
                hint.classList.add('is-ok');
                try { sessionStorage.setItem(STORAGE_KEY, '1'); } catch (e) {}
                dialog.close();
                checkbox.focus();
            });

            // Se o formulário voltou com erro de validação (ex.: senhas
            // diferentes), não obriga a ler tudo de novo na mesma sessão.
            // O checkbox é liberado, mas volta DESMARCADO: o aceite precisa
            // ser dado de novo pelo usuário.
            try {
                if (sessionStorage.getItem(STORAGE_KEY) === '1') {
                    reachedEnd = true;
                    acceptBtn.disabled = false;
                    scrollHint.hidden = true;
                    checkbox.disabled = false;
                    hint.textContent = 'Você já leu os termos nesta sessão. Marque a caixa para concordar.';
                }
            } catch (e) {}

            // Trava no envio: se o checkbox ainda está bloqueado, abre os termos
            form.addEventListener('submit', function (e) {
                if (checkbox.disabled) {
                    e.preventDefault();
                    openTerms();
                }
            });
        })();
    </script>

</x-layouts.guest>
