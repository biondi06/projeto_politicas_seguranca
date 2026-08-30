# Checklist do Projeto — Ecoa

Este checklist tem como objetivo organizar os requisitos de segurança desenvolvidos no projeto **Ecoa** e facilitar a conferência do que já foi implementado e testado.

## 1. Autenticação e Gestão de Credenciais

| Requisito | Implementação | Status |
|---|---|---|
| 1.1 — Hash seguro de senhas | Utilização do Argon2id para proteger as senhas | Concluído |
| 1.2 — Parâmetros de custo | Configuração de memória, tempo e threads do Argon2id | Concluído |
| 1.3 — Salt único | Salt gerado automaticamente pelo Argon2id | Concluído |
| 1.4 — Armazenamento seguro | Senhas armazenadas como hash, sem texto puro | Concluído |
| 1.5 — Autenticação em dois fatores | 2FA utilizando TOTP através do Laravel Fortify | Concluído |
| 1.6 — Validação do 2FA | Código do aplicativo autenticador validado antes da conclusão do login | Concluído |
| 1.7 — Fluxo de autenticação | Cadastro, login, autenticação com 2FA e logout | Concluído |
| 1.8 — Evidências | Capturas dos testes realizadas e organizadas em `docs/evidencias/` | Concluído |
| 1.9 — Duração da sessão | Tempo de duração configurado através de `SESSION_LIFETIME` | Concluído |
| 1.10 — Invalidação da sessão | Sessão encerrada durante o logout | Concluído |
| 1.11 — Proteção contra força bruta | Limite de 5 tentativas por minuto no login e no 2FA | Concluído |
| 1.12 — Justificativas técnicas | Decisões e escolhas de segurança descritas na documentação | Concluído |

---

## 2. Funcionalidades verificadas

### Cadastro e login

- [x] Cadastro de novos usuários
- [x] Validação das credenciais no login
- [x] Redirecionamento para a área autenticada
- [x] Logout através do Laravel Fortify

### Proteção das senhas

- [x] Utilização do Argon2id
- [x] Configuração dos parâmetros de custo
- [x] Utilização de salt automático
- [x] Armazenamento do hash no banco de dados
- [x] Senha não armazenada em texto puro

### Autenticação em dois fatores

- [x] Ativação do 2FA
- [x] Confirmação da senha antes da configuração
- [x] Geração do QR Code
- [x] Utilização de TOTP
- [x] Confirmação do código de autenticação
- [x] Códigos de recuperação disponibilizados pelo Fortify
- [x] Utilização do 2FA durante o processo de login

### Sessões

- [x] Sessão armazenada utilizando o driver configurado
- [x] Tempo de duração da sessão configurável
- [x] Expiração por inatividade
- [x] Invalidação da sessão durante o logout
- [x] Regeneração do token CSRF após o logout

### Proteção contra tentativas excessivas

- [x] Rate Limiting no login
- [x] Limite de 5 tentativas por minuto
- [x] Controle baseado no usuário e endereço IP
- [x] Rate Limiting no processo de autenticação em dois fatores
- [x] Limite de 5 tentativas por minuto no 2FA

---

## 3. Evidências

Os testes realizados durante o desenvolvimento foram registrados em imagens e estão organizados na pasta:

```text
docs/evidencias/
```

Entre as principais evidências estão:

- `cadastro.png` — tela de cadastro;
- `login_realizado.png` — login realizado com sucesso;
- `autenticacao.png` — processo de autenticação;
- `ativar_verificacao.png` — ativação do 2FA;
- `fa_qrcode.png` — QR Code para configuração do autenticador;
- `fa_confirmado.png` — confirmação do segundo fator;
- `seguranca_credenciais.png` — segurança das credenciais;
- `seguranca_credenciais_argon2id_2fa.png` — verificação relacionada ao Argon2id e 2FA;
- `rate_limit.png` — teste do limite de tentativas;
- `tela_limit.png` — bloqueio após excesso de tentativas;
- `sessao_validada.png` — validação da sessão;
- `sessao_invalidada.png` — invalidação da sessão.

---

## 4. Documentação

A documentação detalhada do Requisito 1 está disponível no arquivo:

```text
docs/Requisito 1 — Autenticação e Gestão de Credenciais.md
```

Nela estão descritos o funcionamento do módulo, as configurações utilizadas, as decisões técnicas e os resultados dos testes realizados.

---

## 5. Situação atual

**Requisito 1 — Autenticação e Gestão de Credenciais: CONCLUÍDO**

O código-fonte, a documentação e as evidências referentes ao requisito estão organizados no repositório do projeto.