# Requisito 5 — Auditoria e Logs

Este documento apresenta a implementação de **Auditoria e Logs** no projeto **Ecoa**, desenvolvido com Laravel.

---

# Sumário

1. [Visão geral e arquitetura](#1-visão-geral-e-arquitetura)
2. [Logs de autenticação (5.1) e de falhas/2FA (5.2)](#2-logs-de-autenticação-51-e-de-falhas2fa-52)
3. [Proteção contra alteração dos logs (5.3)](#3-proteção-contra-alteração-dos-logs-53)
4. [Exemplo de análise de logs (5.4)](#4-exemplo-de-análise-de-logs-54)
5. [Justificativas técnicas](#5-justificativas-técnicas)
6. [Evidências de funcionamento](#6-evidências-de-funcionamento)
7. [Checklist dos requisitos](#7-checklist-dos-requisitos)
8. [Considerações finais](#8-considerações-finais)

---

# 1. Visão geral e arquitetura

O Ecoa mantém uma trilha de auditoria própria, separada dos logs técnicos do Laravel (`storage/logs/laravel.log`), armazenada na tabela `logs_auditorias`. Cada evento de segurança relevante (login, falha de login, falha de 2FA) gera um registro **encadeado criptograficamente** ao anterior, permitindo detectar qualquer alteração posterior nos dados.

## Arquivos relacionados

```text
database/migrations/*_create_logs_auditorias_table.php
app/Models/AuditoriaLog.php
app/Http/Middleware/LogTwoFactorOutcome.php
app/Http/Controllers/AuditoriaController.php
app/Providers/AppServiceProvider.php
resources/views/auditoria/index.blade.php
```

---

# 2. Logs de autenticação (5.1) e de falhas/2FA (5.2)

## 2.1 Login bem-sucedido

Capturado via evento nativo `Illuminate\Auth\Events\Login`, disparado pelo Laravel somente quando a autenticação está **completa** — ou seja, após a validação do 2FA, quando ativado:

```php
Event::listen(function (Login $event) {
    AuditoriaLog::registrar('login_sucesso', [
        'user_id' => $event->user->id,
        'email' => $event->user->email,
    ]);
});
```

## 2.2 Falha de login

Capturado via evento nativo `Illuminate\Auth\Events\Failed`, disparado sempre que uma tentativa de autenticação (e-mail/senha) falha:

```php
Event::listen(function (Failed $event) {
    AuditoriaLog::registrar('login_falha', [
        'email' => $event->credentials[Fortify::username()] ?? null,
    ]);
});
```

## 2.3 Falha de 2FA

Diferente do login, uma falha no código de verificação em duas etapas não dispara um evento nativo específico — o Fortify trata isso como erro de validação de formulário. Por isso, foi implementado o middleware `LogTwoFactorOutcome`, que inspeciona o resultado da requisição na rota do desafio 2FA:

```php
if ($request->routeIs('two-factor.login.store') && $request->session()->has('errors')) {
    AuditoriaLog::registrar('2fa_falha', [...]);
}
```

---

# 3. Proteção contra alteração dos logs (5.3)

Cada registro de auditoria armazena dois hashes:

- `hash_anterior`: o hash do registro imediatamente anterior na cadeia;
- `hash_atual`: `SHA-256(hash_anterior + dados_do_evento)`.

```php
$hashAtual = hash('sha256', $hashAnterior . json_encode($payload));
```

Esse desenho — inspirado no mecanismo de encadeamento usado em blockchains — cria uma **cadeia de integridade**: se qualquer campo de um registro antigo for alterado diretamente no banco de dados (por exemplo, por alguém com acesso direto ao MySQL tentando apagar evidência de uma falha de login), o hash recalculado para aquele registro não baterá mais com o `hash_atual` armazenado, e **todos os registros seguintes da cadeia também ficarão inválidos** — tornando a adulteração detectável, mesmo que o `hash_atual` daquele registro específico também seja "corrigido" pelo atacante, a menos que ele recalcule a cadeia inteira a partir daquele ponto.

A verificação é feita por `AuditoriaLog::verificarIntegridade()`, disponível na tela `/auditoria` através do botão "Verificar integridade da cadeia de logs".

---

# 4. Exemplo de análise de logs (5.4)

A tela `/auditoria` (restrita aos perfis Coordenador Clínico e Administrador de TI) apresenta:

- Total de eventos registrados e total de falhas de login;
- **Análise agregada**: tentativas de login malsucedidas agrupadas por endereço IP, ordenadas por frequência — um exemplo prático de como os logs podem ser usados para identificar possíveis tentativas de força bruta ou comportamento suspeito;
- Lista dos últimos 50 eventos, com evento, e-mail, IP, data/hora e hash (truncado).

---

# 5. Justificativas técnicas

**Por que uma tabela própria de auditoria, em vez de usar apenas `storage/logs/laravel.log`?** O arquivo de log padrão do Laravel é texto plano, não estruturado para consulta/análise, e não possui nenhum mecanismo de verificação de integridade — qualquer pessoa com acesso ao arquivo pode editá-lo livremente sem deixar rastro. Uma tabela dedicada, com encadeamento de hash, permite tanto consulta estruturada (SQL) quanto verificação formal de integridade.

**Por que SHA-256 para o encadeamento?** É um algoritmo de hash criptográfico amplamente utilizado e considerado seguro contra colisões na prática atual, adequado para gerar identificadores únicos e verificáveis de cada estado da cadeia.

**Por que restringir a tela de auditoria a Coordenador Clínico e Administrador de TI?** Segue o mesmo princípio de necessidade de acesso já aplicado ao restante do sistema — logs de segurança são informação sensível sobre o funcionamento do sistema e não devem ser visíveis a todos os perfis.

---

# 6. Evidências de funcionamento

```text
docs/evidencias/
```

| Evidência | Arquivo |
|---|---|
| Tela de auditoria com eventos registrados | `auditoria_painel.png` |
| Resultado da verificação de integridade (cadeia íntegra) | `auditoria_integridade_ok.png` |
| Análise de falhas de login por IP | `auditoria_falhas_por_ip.png` |
| Log de falha de 2FA gerado após código incorreto | `auditoria_2fa_falha.png` |

---

# 7. Checklist dos requisitos

| Requisito | Implementação | Status |
|---|---|---|
| 5.1 — Logs de autenticação | Evento `Login` do Laravel | Concluído |
| 5.2 — Logs de falhas e 2FA | Evento `Failed` + middleware `LogTwoFactorOutcome` | Concluído |
| 5.3 — Proteção contra alteração dos logs | Cadeia de hash SHA-256 (`AuditoriaLog::verificarIntegridade`) | Concluído |
| 5.4 — Exemplo de análise de logs | Tela `/auditoria` — falhas de login por IP | Concluído |

---

# 8. Considerações finais

A trilha de auditoria do Ecoa foi desenhada para ser tanto **consultável** (tela dedicada, com análise agregada) quanto **verificável** (cadeia de hash), atendendo ao princípio de accountability da segurança da informação: não basta registrar eventos, é preciso garantir que o registro não possa ser adulterado silenciosamente depois do fato.
