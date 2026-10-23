# Controle de Acesso e Provisionamento de Perfis

Este documento descreve como as contas do **Ecoa** recebem o seu perfil de acesso, a falha de segurança que existia nesse ponto e a solução adotada.

---

# Sumário

1. [Contexto e problema](#1-contexto-e-problema)
2. [Decisão de projeto](#2-decisão-de-projeto)
3. [Fluxos](#3-fluxos)
4. [Modelo de dados](#4-modelo-de-dados)
5. [Medidas de segurança](#5-medidas-de-segurança)
6. [Arquivos relacionados](#6-arquivos-relacionados)
7. [Testes](#7-testes)
8. [Justificativas técnicas](#8-justificativas-técnicas)
9. [Limitações conhecidas](#9-limitações-conhecidas)

---

# 1. Contexto e problema

O Ecoa tem quatro perfis: Fonoaudiólogo, Coordenador Clínico, Administrador de TI e Responsável Legal. Os três primeiros são profissionais: exigem verificação em duas etapas e têm acesso a áreas restritas, como a trilha de auditoria.

**A falha.** O formulário de cadastro enviava o campo `perfil`, e o servidor o aceitava sem conferir. Qualquer visitante podia se cadastrar como Administrador de TI (ou abrir o formulário, alterar o valor do campo e enviá-lo) e passava a acessar `/auditoria`, que expõe e-mails e endereços IP de outros usuários. A tentativa de ataque custava segundos e não exigia nenhum conhecimento técnico.

**Causa.** O perfil era tratado como um dado informado pelo usuário, quando na verdade é uma **decisão de autorização**, que só o sistema pode tomar.

---

# 2. Decisão de projeto

Três princípios guiaram a correção:

1. **O perfil nunca vem do formulário.** Quem decide é o servidor.
2. **Contas profissionais nascem de convite.** Um Administrador de TI informa o e-mail e o perfil, o Ecoa envia o convite por e-mail, e a própria pessoa cria a senha. O administrador não vê nem define a senha de ninguém.
3. **O cadastro aberto continua existindo**, mas só cria o perfil de menor privilégio: Responsável Legal.

Isso segue o próprio conceito do projeto, em que o Administrador de TI "gerencia contas e permissões".

---

# 3. Fluxos

## 3.1 Cadastro aberto (sem convite)

```text
Visitante → /register → informa nome, e-mail, senha e aceita os termos
        → CreateNewUser ignora qualquer campo "perfil"
        → conta criada como Responsável Legal
```

## 3.2 Cadastro com convite (contas profissionais)

```text
Administrador de TI → /administracao/convites (atalho na home e no menu da conta)
        → informa e-mail e perfil → convite criado
        → o Ecoa envia o convite por e-mail à pessoa; o administrador não vê o link
        → se o e-mail não puder ser enviado, o link aparece UMA vez para o
          administrador entregá-lo por outro canal (o convite nunca se perde)

Pessoa convidada → abre o link /register?convite=...
        → e-mail já preenchido e travado → cria a própria senha e aceita os termos
        → CreateNewUser valida o convite (existe, pendente, para este e-mail)
        → conta criada com o perfil do convite; convite marcado como usado
        → no primeiro acesso, o sistema exige a ativação do 2FA
```

## 3.3 Primeiro administrador

Como só um administrador emite convites, o primeiro é criado pelo `AdministradorInicialSeeder`, com e-mail e senha vindos de **variáveis de ambiente** (nunca do repositório). O seeder é idempotente: se o e-mail já existe, nada é alterado.

## 3.4 Reenvio e revogação

- **Reenviar:** o token de um convite já criado só existe como hash e não pode ser lido de volta. Reenviar significa revogar o convite atual e emitir um novo, igual, com um link novo, que é enviado por e-mail.
- **Revogar:** cancela um convite pendente; o link deixa de funcionar.

## 3.5 Acesso às ferramentas de administração

Quem tem permissão encontra os atalhos sem precisar digitar endereços:

- na **home**, na seção "Administração" (cartões de acesso rápido);
- no **menu da conta** (canto superior direito);
- na **barra superior** das próprias páginas de Auditoria e Acessos profissionais, que levam uma à outra.

Cada atalho só aparece para o perfil que pode usá-lo: "Acessos profissionais" somente para o Administrador de TI; "Auditoria e logs" para o Coordenador Clínico e o Administrador de TI. Os demais perfis não veem nenhum dos dois.

---

# 4. Modelo de dados

Tabela `convites_acesso`:

| Coluna | Descrição |
|---|---|
| `token_hash` | SHA-256 do token do link (único). O token em si nunca é gravado. |
| `email` | E-mail para o qual o convite vale. |
| `perfil` | Perfil que a conta receberá. |
| `criado_por` | Administrador que emitiu o convite. |
| `usado_por`, `usado_em` | Conta criada a partir do convite e quando. |
| `revogado_em` | Preenchido se o administrador cancelou o convite. |
| `expira_em` | Fim da validade (72 horas por padrão). |

A situação de um convite é derivada: `pendente`, `usado`, `revogado` ou `expirado`.

---

# 5. Medidas de segurança

| Medida | Como funciona |
|---|---|
| Perfil decidido no servidor | `CreateNewUser` define o perfil e usa `forceFill`; o campo `perfil` do formulário é ignorado. |
| Token aleatório | 48 caracteres aleatórios; adivinhar um link válido é inviável. |
| Hash no banco | Só o SHA-256 é gravado: ler a tabela não permite montar um link. |
| Uso único | O convite é consumido na mesma transação que cria a conta. |
| Concorrência | `lockForUpdate`: dois cadastros simultâneos com o mesmo convite não passam os dois. |
| E-mail fixo | O link não serve para outra pessoa: o e-mail digitado precisa ser o do convite. |
| Validade e revogação | O convite expira sozinho e pode ser cancelado enquanto pendente. |
| Entrega por e-mail | O link do convite vai direto ao e-mail da pessoa convidada. Só quem recebe a mensagem tem o link. |
| Alternativa sem e-mail | Se o ambiente não tem e-mail configurado (driver `log` ou `array`) ou o envio falha, o link é mostrado uma única vez ao administrador. |
| Mensagem única de recusa | Convite inexistente, expirado, usado ou de outro e-mail geram a mesma mensagem. |
| Autorização centralizada | Gates `gerenciar-acessos` (só administrador) e `ver-auditoria` ficam definidos num único lugar. |
| 2FA obrigatório | Contas profissionais criadas por convite precisam ativar o 2FA no primeiro acesso. |
| Auditoria | Eventos `convite_criado`, `convite_usado`, `convite_revogado` e `administrador_inicial_criado` entram na trilha com verificação de integridade. |

---

# 6. Arquivos relacionados

```text
app/Enums/Perfil.php                                  → os perfis, em um só lugar
app/Actions/Fortify/CreateNewUser.php                 → decide o perfil; valida e consome o convite
app/Models/ConviteAcesso.php                          → regras do convite
app/Http/Controllers/ConviteAcessoController.php      → emitir, enviar por e-mail, reenviar e revogar convites
app/Notifications/ConviteAcessoNotification.php       → e-mail do convite (tema visual do Ecoa)
app/Http/Controllers/AuditoriaController.php          → passa a usar o Gate "ver-auditoria"
app/Providers/AppServiceProvider.php                  → define os Gates e entrega o convite ao cadastro
database/migrations/*_create_convites_acesso_table.php
database/seeders/AdministradorInicialSeeder.php
config/ecoa.php
resources/views/administracao/convites.blade.php
resources/views/home.blade.php                        → seção "Administração" e atalhos no menu da conta
resources/views/auth/register.blade.php               → sem seletor de perfil
routes/web.php
tests/Feature/CadastroPerfilTest.php
tests/Feature/ConviteAcessoTest.php
tests/Feature/AtalhosDeAdministracaoTest.php
tests/Concerns/CriaUsuarios.php                       → atalhos para os testes criarem usuários
```

---

# 7. Testes

Executados com `php artisan test`. Cobrem:

- cadastro aberto ignora o perfil enviado e cria Responsável Legal;
- cadastro exige o aceite dos termos e registra o consentimento com a versão;
- Responsável Legal não acessa `/auditoria` nem a gestão de convites (403);
- cadastro com convite cria o perfil do convite, mesmo que o formulário envie outro;
- convite usado, expirado, revogado, de outro e-mail ou inventado é recusado;
- só o administrador acessa a gestão de convites;
- o convite é gravado só como hash, e o link aparece uma única vez;
- não se emite convite para Responsável Legal nem para e-mail que já tem conta;
- o administrador revoga um convite pendente;
- o convite é enviado por e-mail à pessoa certa, e o administrador não vê o link quando o e-mail é entregue;
- sem envio real de e-mail, o link é mostrado ao administrador uma única vez;
- o e-mail traz o link, o perfil e o nome de quem convidou;
- reenviar revoga o convite antigo e emite um novo; convite que não está pendente não é reenviado;
- só o administrador reenvia convites;
- os atalhos de administração aparecem na home apenas para quem tem permissão.

**Verificação manual pelo front-end** (para a evidência da avaliação):

1. Em `/register` não existe mais seletor de perfil.
2. Alterar o formulário pelo DevTools para enviar `perfil=administrador_ti` cria uma conta de Responsável Legal, e `/auditoria` responde 403.
3. O administrador envia um convite, a pessoa recebe o e-mail, o link abre o cadastro com o e-mail travado, e a conta nasce com o perfil do convite.
4. Reabrir o mesmo link depois do cadastro mostra "convite inválido ou expirou".

---

# 8. Justificativas técnicas

**Por que convite, e não o administrador criar a conta com uma senha provisória?** Assim o administrador nunca conhece a senha de ninguém, o que elimina um risco de segurança e de responsabilização. A pessoa define o próprio segredo desde o início.

**Por que guardar só o hash do token?** É o mesmo princípio usado para tokens de recuperação de senha: um vazamento de leitura do banco não permite criar contas profissionais.

**Por que um enum de perfis?** O texto `administrador_ti` estava escrito à mão em vários arquivos. Centralizar evita erros de digitação e deixa claro quais perfis são profissionais.

**Por que um Gate para a auditoria?** A regra de quem pode ver a trilha ficava repetida num controller. Num único ponto, uma mudança de regra não deixa nenhuma tela para trás.

---

# 9. Limitações conhecidas

- O envio do convite por e-mail depende da configuração de e-mail do ambiente (`MAIL_MAILER` e credenciais). Quando o provedor bloqueia o envio ou aceita apenas destinatários de teste, o sistema mostra o link ao administrador como alternativa.
- Não há tela para desativar ou alterar o perfil de uma conta existente; isso continua sendo feito diretamente no banco.
- A página de gestão de convites não tem paginação (mostra os 50 mais recentes).
- O texto dos Termos de Uso, seção 3, foi ajustado para descrever que o perfil é definido pelo Ecoa; se o grupo quiser rigor de versionamento, deve subir `ECOA_TERMOS_VERSAO` para `v1.1`.
