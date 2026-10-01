<div align="center">

# Ecoa

**Sistema de Acompanhamento Fonoaudiológico Infantil**

Projeto avaliativo do curso de Sistemas de Informação — Universidade de Mogi das Cruzes (6º semestre, 2026)

[![DevSecOps Pipeline](https://github.com/biondi06/projeto_politicas_seguranca/actions/workflows/devsecops-pipeline.yml/badge.svg)](https://github.com/biondi06/projeto_politicas_seguranca/actions/workflows/devsecops-pipeline.yml)
![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-9.0-4479A1?logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/license-BSD--3--Clause-blue)

</div>

---

## Sobre o projeto

O **Ecoa** é um sistema que atua como elo entre o fonoaudiólogo, demais profissionais envolvidos no caso (pediatra, terapeuta ocupacional) e a coordenação clínica, organizando planos terapêuticos fonoaudiológicos, uma biblioteca de materiais de estimulação da fala e o acompanhamento estruturado da evolução de crianças em terapia de desenvolvimento da fala.

Como o sistema trata **dado pessoal sensível** (dado de saúde de crianças, conforme Art. 5º, II da LGPD), segurança e conformidade com a LGPD foram tratadas como parte da arquitetura desde a concepção do projeto — não como camada adicionada posteriormente.

### Problema real que o sistema resolve

Crianças em acompanhamento de desenvolvimento da fala (atraso de fala, trocas fonéticas, gagueira, entre outros) costumam ser atendidas por um fonoaudiólogo que trabalha isolado da escola, do pediatra e de outros especialistas envolvidos no caso, porque:

- Não há canal estruturado para o fonoaudiólogo compartilhar o plano terapêutico com pediatra e terapeuta ocupacional que acompanham a mesma criança;
- Falta material padronizado de estimulação da fala que os pais possam reproduzir corretamente em casa (hoje isso costuma ser passado em papel avulso ou verbalmente);
- O acompanhamento do progresso da criança é feito de forma informal (anotações soltas), sem histórico estruturado que ajude a ajustar a terapia ao longo do tempo.

---

## Funcionalidades

### Núcleo clínico (v1)
- Cadastro de crianças com Plano Terapêutico Fonoaudiológico (tipo de dificuldade, fonemas-alvo, metas)
- Biblioteca de exercícios de estimulação da fala, categorizados por fonema e dificuldade
- Plano terapêutico colaborativo entre fonoaudiólogos e demais profissionais
- Diário de evolução da fala, com histórico estruturado
- Painel do Coordenador Clínico

### Segurança e conformidade
- **Autenticação:** Laravel Fortify, hash de senhas com **Argon2id** (parâmetros explícitos e documentados), proteção contra força bruta (rate limiting)
- **2FA (TOTP):** obrigatório para perfis profissionais (Fonoaudiólogo, Coordenador Clínico, Administrador de TI), com QR code e códigos de recuperação
- **Recuperação de senha:** token criptograficamente seguro, expiração em 60 minutos, uso único, com log de solicitação/sucesso/falha
- **Criptografia em repouso:** campos clínicos sensíveis protegidos com **AES-256-CBC**
- **HTTPS obrigatório:** redirecionamento automático e reconhecimento correto de proxy reverso em produção
- **Conformidade com a LGPD:** consentimento explícito e versionado no cadastro, consulta, exportação (JSON) e exclusão/anonimização dos dados pelo próprio titular (`/meus-dados`)
- **Auditoria:** trilha de logs de segurança com **encadeamento de hash SHA-256** (detecção de adulteração), painel de análise restrito a perfis autorizados (`/auditoria`)
- **DevSecOps:** pipeline de CI com varredura de segredos (TruffleHog), análise de vulnerabilidades em dependências (Trivy) e análise estática de código (Semgrep), bloqueando merges com falhas de severidade alta/crítica

---

## Stack

| Camada | Tecnologia |
|---|---|
| Backend | Laravel 13 (PHP 8.4), arquitetura MVC |
| Autenticação | Laravel Fortify |
| Frontend | Bootstrap (última versão) + jQuery |
| Banco de dados | MySQL 9.0 |
| Build de assets | Vite |
| CI/CD de segurança | GitHub Actions (TruffleHog, Trivy, Semgrep) |
| Hospedagem | Railway |

---

## Estrutura do projeto

```text
app/
 Actions/Fortify/        # Criação de usuário, reset de senha (com consentimento LGPD)
 Http/Controllers/       # LgpdController, AuditoriaController, SecurityController...
 Http/Middleware/        # EnsureHttps, EnsureTwoFactorIsEnabled, LogTwoFactorOutcome...
 Models/                 # Crianca, PlanoTerapeutico, Consentimento, AuditoriaLog...
database/migrations/     # 15+ migrations (entidades clínicas + segurança/LGPD)
resources/views/
 auth/                   # Login, registro, 2FA, recuperação de senha
 lgpd/                   # Painel "Meus Dados"
 auditoria/              # Painel de auditoria
docs/
 Requisito 1 a 5...md   # Documentação técnica de cada módulo de segurança
 evidencias/             # Capturas de tela dos testes funcionais
 CHECKLIST.md             # Checklist consolidado do projeto
.github/workflows/       # Pipeline DevSecOps

---

## Como rodar o projeto localmente

```bash
composer install
npm install
php artisan key:generate
php artisan migrate
php artisan serve
npm run build
```

Acesse em `http://127.0.0.1:8000`
