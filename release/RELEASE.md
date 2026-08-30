# Release Backend - Projeto Ecoa

## Data da release

- 2026-08-30

---

# Visão geral

O backend do Ecoa foi estruturado para atender os requisitos de autenticação e gestão de credenciais do projeto, utilizando Laravel 12 e Laravel Fortify como base para os mecanismos de segurança.

Nesta release, o foco principal foi a implementação do fluxo completo de autenticação, incluindo cadastro de usuários, login seguro, autenticação em dois fatores (2FA), gerenciamento de sessão, proteção contra força bruta e recuperação de credenciais.

---

# Status atual do backend

## Funcionalidades entregues

1. Cadastro de usuários.
2. Autenticação utilizando e-mail e senha.
3. Armazenamento seguro de senhas utilizando bcrypt.
4. Utilização automática de salt criptográfico individual por usuário.
5. Controle de sessão autenticada.
6. Redirecionamento seguro para área autenticada após login.
7. Logout seguro através do Laravel Fortify.
8. Recuperação de senha baseada em token.
9. Redefinição de senha através de link seguro.
10. Implementação de autenticação em duas etapas (2FA).
11. Suporte a códigos de recuperação.
12. Suporte a Passkeys/WebAuthn.
13. Proteção contra força bruta através de Rate Limiting.
14. Limitação de tentativas de autenticação para login e 2FA.
15. Views customizadas para todos os fluxos de autenticação.

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
- Configuração do fluxo de autenticação.

---

## Views

### Públicas

- ecoa.blade.php

### Autenticação

- auth/login.blade.php
- auth/register.blade.php
- auth/forgot-password.blade.php
- auth/reset-password.blade.php
- auth/two-factor-challenge.blade.php

### Área Autenticada

- home.blade.php

---

## Configurações

### config/fortify.php

Funcionalidades habilitadas:

- Registro de usuários
- Recuperação de senha
- Atualização de perfil
- Atualização de senha
- Autenticação em dois fatores

### config/auth.php

Configurações de autenticação:

- Guard web
- Provider eloquent
- Reset de senha
- Timeout de confirmação

---

# Fluxo de autenticação implementado

## Cadastro

1. O usuário acessa a página de cadastro.
2. Os dados são enviados ao Fortify.
3. O usuário é criado utilizando CreateNewUser.
4. A senha é armazenada utilizando bcrypt.
5. O sistema autentica automaticamente o usuário.
6. O usuário é redirecionado para a área autenticada.

---

## Login

1. O usuário informa e-mail e senha.
2. O Fortify valida as credenciais.
3. O sistema verifica o limite de tentativas.
4. Caso as credenciais sejam válidas:
   - segue para o dashboard;
   - ou inicia o fluxo de 2FA.
5. Caso falhem:
   - mensagem de erro é exibida;
   - tentativa é contabilizada.

---

## Verificação em 2FA

1. O sistema solicita a confirmação em duas etapas.
2. O usuário informa o código do aplicativo autenticador.
3. O sistema valida o código.
4. Caso válido:
   - acesso liberado.
5. Caso inválido:
   - tentativa rejeitada.

---

## Recuperação de senha

1. Usuário informa o e-mail cadastrado.
2. O sistema gera um token seguro.
3. Um link de redefinição é disponibilizado.
4. O token é utilizado para cadastrar uma nova senha.
5. A nova senha é armazenada utilizando bcrypt.

---

## Logout

1. O usuário encerra a sessão.
2. A sessão atual é invalidada.
3. O token da sessão é regenerado.
4. O usuário retorna à página pública.

---

# Componentes e módulos principais

## Laravel Fortify

Responsável por:

- Login
- Cadastro
- Logout
- Recuperação de senha
- Reset de senha
- 2FA
- Passkeys

---

## Rate Limiter

Proteções implementadas:

### Login

- 5 tentativas por minuto.

### Two Factor

- 5 tentativas por minuto.

### Passkeys

- 10 tentativas por minuto.

---

## Models

Model principal de autenticação:

- User

Modelos de domínio já estruturados:

- Crianca
- ResponsavelLegal
- Fonoaudiologo
- CoordenadorClinico
- AdministradorTi
- PlanoTerapeutico
- Exercicio
- ComentarioPlano
- RegistroEvolucao
- RegistroExercicioRealizado
- ArquivoVideo

---

# Segurança implementada

## Credenciais

- Hash bcrypt.
- Salt individual automático.
- Armazenamento seguro de senhas.

## Autenticação

- Login protegido.
- Logout seguro.
- Recuperação de senha.
- 2FA.
- Passkeys.

## Proteção contra ataques

- Rate Limiting.
- Controle de sessão.
- Middleware de autenticação.
- Proteção CSRF.

---

# Estado atual do backend

## Concluído

1. Estrutura principal do projeto Laravel.
2. Instalação e configuração do Fortify.
3. Cadastro.
4. Login.
5. Logout.
6. Recuperação de senha.
7. Reset de senha.
8. Autenticação em duas etapas.
9. Passkeys.
10. Controle de sessão.
11. Proteção contra força bruta.
12. Área autenticada inicial.

---

## Em andamento

1. Auditoria e logs.
2. Documentação dos fluxos de autenticação.
3. Evidências dos requisitos de segurança.
4. Adequação dos requisitos LGPD.
5. Desenvolvimento dos módulos clínicos.

---

# Observações

1. Todas as rotas de autenticação registradas pelo Laravel Fortify estão operacionais.
2. O sistema foi validado através do comando `php artisan route:list`.
3. O fluxo de autenticação encontra-se funcional para login, cadastro, logout e 2FA.
4. A estrutura atual atende à implementação dos requisitos de autenticação e gestão de credenciais previstos para a primeira etapa do projeto.