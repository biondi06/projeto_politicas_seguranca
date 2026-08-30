# Release Backend — Projeto Ecoa

## Data da release

- 2026-08-30

---

# Visão geral

O backend do Ecoa foi estruturado para atender aos requisitos de autenticação e gestão de credenciais do projeto, utilizando Laravel 12 e Laravel Fortify como base para os mecanismos de segurança.

Nesta release, foi implementado o fluxo de autenticação e gestão de credenciais, incluindo cadastro de usuários, login, armazenamento seguro de senhas com Argon2id, autenticação em dois fatores (2FA), gerenciamento de sessões, logout, recuperação e redefinição de senha e proteção contra tentativas excessivas de autenticação.

Além da implementação, foram produzidas a documentação técnico-científica, as evidências de funcionamento e o checklist correspondente ao requisito.

---

# Status atual do backend

## Funcionalidades implementadas

1. Cadastro de usuários.
2. Autenticação utilizando e-mail e senha.
3. Armazenamento seguro de senhas utilizando Argon2id.
4. Utilização automática de salt individual pelo mecanismo de hashing.
5. Controle de sessão autenticada.
6. Redirecionamento para a área autenticada após login.
7. Logout através do Laravel Fortify.
8. Invalidação da sessão durante o logout.
9. Regeneração do token CSRF após o logout.
10. Autenticação em dois fatores utilizando TOTP.
11. Confirmação do 2FA através de código temporário.
12. Geração de QR Code para configuração do autenticador.
13. Códigos de recuperação para o 2FA.
14. Rate Limiting para tentativas de login.
15. Rate Limiting para tentativas de autenticação em dois fatores.
16. Rate Limiting para operações relacionadas a Passkeys.
17. Views customizadas para os fluxos de autenticação.
18. Área de segurança para gerenciamento do 2FA.
19. Documentação técnico-científica do requisito.
20. Evidências dos testes realizados.
21. Checklist do projeto.

---

# Estrutura atual

## Providers

### AppServiceProvider

Responsável pelo bootstrap geral da aplicação.

### FortifyServiceProvider

Responsável por:

- Registro dos componentes de autenticação.
- Registro das views customizadas.
- Configuração dos Rate Limiters.
- Configuração dos fluxos de autenticação.

---

## Controllers

### SecurityController

Responsável pela exibição da área de segurança da conta, utilizada para o gerenciamento da autenticação em dois fatores.

---

## Views

### Públicas

- `ecoa.blade.php`

### Autenticação

- `auth/login.blade.php`
- `auth/register.blade.php`
- `auth/forgot-password.blade.php`
- `auth/reset-password.blade.php`
- `auth/two-factor-challenge.blade.php`
- `auth/confirm-password.blade.php`

### Área autenticada

- `home.blade.php`

### Segurança

- `security.blade.php`

---

## Configurações

### `config/fortify.php`

Funcionalidades habilitadas:

- Registro de usuários.
- Atualização de perfil.
- Autenticação em dois fatores.

Também estão configurados os limitadores de tentativas para login, 2FA e Passkeys.

### `config/hashing.php`

Define o Argon2id como algoritmo utilizado para o hashing das senhas.

### `config/session.php`

Define as configurações relacionadas às sessões, incluindo:

- Driver de sessão.
- Tempo de duração.
- Expiração ao fechar o navegador.
- Cookies de sessão.
- Proteções relacionadas ao cookie.

---

# Fluxo de autenticação implementado

## Cadastro

1. O usuário acessa a página de cadastro.
2. Os dados são enviados ao Fortify.
3. O usuário é criado utilizando `CreateNewUser`.
4. A senha é protegida utilizando Argon2id.
5. O sistema realiza a autenticação.
6. O usuário é direcionado para a área autenticada.

---

## Login

1. O usuário informa e-mail e senha.
2. O Fortify valida as credenciais.
3. O sistema aplica o Rate Limiting configurado.
4. Caso as credenciais sejam válidas, o usuário é autenticado.
5. Caso o 2FA esteja confirmado, o sistema solicita o segundo fator.
6. Após a validação, o acesso à área autenticada é liberado.

---

## Verificação em 2FA

1. O usuário ativa o 2FA pela área de segurança.
2. O sistema gera o segredo TOTP.
3. Um QR Code é apresentado para configuração do aplicativo autenticador.
4. O usuário informa o código gerado.
5. O sistema valida o código.
6. Após a confirmação, o segundo fator passa a fazer parte do processo de autenticação.
7. Códigos de recuperação também são disponibilizados.

---

## Logout

1. O usuário solicita o logout.
2. O Laravel Fortify encerra a autenticação.
3. A sessão atual é invalidada.
4. O token CSRF é regenerado.
5. O usuário deixa de estar autenticado.

A rota utilizada é:

```text
POST /logout
```

---

# Componentes e módulos principais

## Laravel Fortify

Responsável pelos principais fluxos de autenticação:

- Login.
- Cadastro.
- Logout.
- Recuperação de senha.
- Redefinição de senha.
- Atualização de perfil.
- Atualização de senha.
- Autenticação em dois fatores.
- Recursos relacionados a Passkeys.

---

## Rate Limiter

Proteções implementadas:

### Login

- 5 tentativas por minuto.
- Controle baseado no identificador utilizado no login e no endereço IP.

### Two Factor

- 5 tentativas por minuto.
- Controle associado à sessão de autenticação.

### Passkeys

- 10 tentativas por minuto.
- Controle associado à credencial e ao endereço IP.

---

# Segurança implementada

## Credenciais

- Hash utilizando Argon2id.
- Salt individual gerado automaticamente.
- Parâmetros de custo configurados.
- Senhas não armazenadas em texto puro.

## Autenticação

- Login protegido.
- Logout seguro.
- Recuperação de senha.
- Redefinição de senha.
- Autenticação em dois fatores.
- Códigos de recuperação.

## Sessões

- Sessões armazenadas utilizando o driver configurado.
- Tempo de duração configurável.
- Invalidação da sessão durante o logout.
- Regeneração do token CSRF.

## Proteção contra ataques

- Rate Limiting.
- Middleware de autenticação.
- Proteção CSRF.
- Controle de tentativas de login.
- Controle de tentativas de 2FA.

---

# Evidências e documentação

A documentação do requisito foi organizada dentro do diretório:

```text
docs/
```

Incluindo:

- `Requisito 1 — Autenticação e Gestão de Credenciais.md`
- `Checklist do Projeto — Ecoa.md`
- Evidências dos testes funcionais.

As evidências estão organizadas em:

```text
docs/evidencias/
```

O diretório contém capturas relacionadas a:

- Cadastro.
- Login.
- Autenticação.
- Ativação do 2FA.
- QR Code.
- Confirmação do 2FA.
- Segurança das credenciais.
- Rate Limiting.
- Sessão validada.
- Sessão invalidada.

---

# Estado atual do backend

## Concluído

1. Estrutura principal do projeto Laravel.
2. Instalação e configuração do Laravel Fortify.
3. Cadastro de usuários.
4. Login.
5. Logout.
6. Atualização de perfil.
7. Autenticação em dois fatores.
8. Gerenciamento de sessão.
9. Proteção contra força bruta.
10. Área autenticada inicial.
11. Área de segurança da conta.
12. Configuração do Argon2id.
13. Documentação técnico-científica.
14. Evidências de funcionamento.
15. Checklist do projeto.

---

## Próximas etapas

As próximas etapas do projeto correspondem aos demais requisitos e módulos previstos para o desenvolvimento do Ecoa, incluindo os recursos relacionados ao sistema clínico, auditoria, logs e demais requisitos de segurança e proteção de dados.

---

# Observações

1. As rotas de autenticação disponibilizadas pelo Laravel Fortify foram verificadas através do comando `php artisan route:list`.
2. A rota de logout está registrada como `POST /logout`.
3. O driver de hashing utilizado pela aplicação foi confirmado como `argon2id` através do Laravel Tinker.
4. Os parâmetros do Argon2id foram verificados no ambiente de desenvolvimento.
5. O fluxo de cadastro, login, 2FA, logout e gerenciamento de sessão foi testado através da aplicação.
6. O Rate Limiting foi configurado para login, 2FA e Passkeys.
7. As evidências dos testes foram organizadas no diretório `docs/evidencias/`.
8. A documentação técnico-científica e o checklist foram incluídos no repositório junto ao código-fonte.
9. Esta release consolida a primeira etapa do módulo de autenticação e gestão de credenciais do Ecoa.