# Requisito 4 — Conformidade com a LGPD

Este documento apresenta a implementação da **Conformidade com a LGPD** no projeto **Ecoa**, desenvolvido com Laravel.

---

# Sumário

1. [Visão geral](#1-visão-geral)
2. [Listagem dos dados pessoais coletados e finalidades](#2-listagem-dos-dados-pessoais-coletados-e-finalidades)
3. [Minimização de dados](#3-minimização-de-dados)
4. [Consentimento](#4-consentimento)
5. [Direitos do titular](#5-direitos-do-titular)
6. [Justificativas técnicas](#6-justificativas-técnicas)
7. [Evidências de funcionamento](#7-evidências-de-funcionamento)
8. [Checklist dos requisitos](#8-checklist-dos-requisitos)
9. [Considerações finais](#9-considerações-finais)

---

# 1. Visão geral

O Ecoa trata dado pessoal sensível (dado de saúde de crianças em desenvolvimento de fala, conforme Art. 5º, II da LGPD). Este requisito trata da conformidade do sistema com os princípios e direitos previstos na Lei Geral de Proteção de Dados: transparência sobre os dados coletados, finalidade declarada, minimização, consentimento explícito e revogável, e os direitos de acesso, exportação e exclusão pelo titular.

## Principais arquivos relacionados

```text
database/migrations/*_create_consentimentos_table.php
database/migrations/*_add_soft_deletes_to_users_table.php
app/Models/Consentimento.php
app/Http/Controllers/LgpdController.php
app/Actions/Fortify/CreateNewUser.php
resources/views/lgpd/meus-dados.blade.php
resources/views/auth/register.blade.php
routes/web.php
```

---

# 2. Listagem dos dados pessoais coletados e finalidades

| Dado | Tabela | Finalidade | Base legal (LGPD) |
|---|---|---|---|
| Nome, e-mail, senha | `users` | Autenticação e identificação do profissional/responsável no sistema | Execução de contrato / legítimo interesse |
| Perfil de acesso | `users` | Controle de permissões por função | Legítimo interesse |
| Nome, contato, e-mail do responsável | `responsavel_legals` | Vínculo com a criança acompanhada e comunicação sobre o atendimento | Consentimento |
| Nome, data de nascimento da criança | `criancas` | Identificação do paciente para o acompanhamento terapêutico | Consentimento dos pais/responsável (Art. 14) |
| Tipo de dificuldade, fonemas-alvo, metas | `plano_terapeuticos` | Registro do plano terapêutico fonoaudiológico (dado de saúde) | Consentimento + tutela da saúde (Art. 11) |
| Comentários entre profissionais | `comentario_planos` | Colaboração clínica entre fonoaudiólogo e demais especialistas | Consentimento + tutela da saúde |
| Observações de evolução | `registro_evolucaos` | Acompanhamento da evolução terapêutica da criança | Consentimento + tutela da saúde |
| Registro de consentimento | `consentimentos` | Comprovação do consentimento dado pelo titular | Obrigação legal (LGPD) |

---

# 3. Minimização de dados

O sistema coleta apenas o necessário para a finalidade terapêutica, evitando dado clínico além do escopo fonoaudiológico:

- **Não é coletado**: diagnóstico médico completo, histórico de saúde geral, prontuário médico, CPF ou documentos de identidade da criança;
- **É coletado apenas**: indicações pedagógico-terapêuticas relacionadas especificamente ao desenvolvimento da fala (fonemas-alvo, metas, evolução);
- Essa decisão foi tomada desde a concepção do sistema (documento de concepção do Ecoa), e não como ajuste posterior — caracterizando *privacy by design*.

---

# 4. Consentimento

## 4.1 Registro explícito (4.4)

No cadastro (`/register`), o usuário deve marcar obrigatoriamente a caixa de aceite dos Termos de Uso e Política de Privacidade. A validação (`app/Actions/Fortify/CreateNewUser.php`) exige:

```php
'aceite_lgpd' => ['required', 'accepted'],
```

Sem essa marcação, o cadastro não é concluído.

## 4.2 Associação à finalidade (4.5) e registro de data/versão (4.7)

Ao aceitar, um registro é criado na tabela `consentimentos`, associando o consentimento a uma finalidade específica, com data/hora e versão do termo vigente:

```php
Consentimento::create([
    'user_id' => $user->id,
    'finalidade' => 'Uso da plataforma Ecoa e tratamento de dados terapêuticos da criança acompanhada',
    'versao_termo' => 'v1.0',
    'aceito_em' => now(),
    'ip' => request()->ip(),
]);
```

## 4.3 Revogação (4.6)

O usuário pode revogar o consentimento a qualquer momento, na tela "Meus Dados" (`/meus-dados`). A revogação não apaga o histórico — apenas marca a data de revogação (`revogado_em`), preservando o registro para fins de auditoria:

```php
Consentimento::where('user_id', $request->user()->id)
    ->whereNull('revogado_em')
    ->update(['revogado_em' => now()]);
```

---

# 5. Direitos do titular

## 5.1 Consulta aos dados (4.8)

A tela `/meus-dados` (`LgpdController@meusDados`) exibe ao usuário logado todos os seus dados cadastrais e o histórico completo de consentimentos.

## 5.2 Exportação (4.9)

O botão "Exportar meus dados" gera um arquivo `.json` com os dados do usuário e seus consentimentos, para download imediato (`LgpdController@exportar`).

## 5.3 Exclusão (4.10)

O botão "Excluir meus dados" aciona `LgpdController@excluir`, que:
1. Anonimiza os campos identificáveis (`name`, `email`);
2. Aplica exclusão lógica (*soft delete*) via `SoftDeletes`, preservando apenas o necessário para integridade referencial;
3. Encerra a sessão do usuário imediatamente.

## 5.4 Fluxo de atendimento aos direitos (4.11)

```text
Titular solicita acesso/exportação/exclusão
        |
        v
Autenticação obrigatória (só o próprio titular acessa seus dados)
        |
        v
   /meus-dados (consulta)  --->  /meus-dados/exportar (download JSON)
        |
        v
   Revogar consentimento (marca revogado_em)
        |
        v
   Excluir dados (anonimiza + soft delete + logout)
```

Todas as ações ocorrem dentro da própria aplicação, sem necessidade de solicitação manual/e-mail — atendendo ao princípio de facilitação do exercício de direitos previsto na LGPD.

---

# 6. Justificativas técnicas

**Por que soft delete em vez de exclusão física imediata?** A exclusão física de todos os registros vinculados (planos terapêuticos, registros de evolução) poderia comprometer a integridade referencial e o histórico clínico de outras pessoas envolvidas (ex.: comentários de outro profissional). A anonimização dos dados identificáveis do usuário, combinada com soft delete, atende ao direito de eliminação sem comprometer a integridade dos demais registros do sistema.

**Por que versionar o termo de consentimento?** Registrar a versão (`versao_termo`) permite identificar exatamente qual texto o usuário aceitou, mesmo que a política de privacidade seja atualizada no futuro — evitando ambiguidade sobre o que foi de fato consentido.

**Por que revogação não apaga o histórico?** Manter o registro de que houve consentimento e quando foi revogado é, em si, uma exigência de comprovação (accountability) prevista na LGPD — apagar esse histórico eliminaria a prova de conformidade.

---

# 7. Evidências de funcionamento

```text
docs/evidencias/
```

| Evidência | Arquivo |
|---|---|
| Checkbox de consentimento no cadastro | `lgpd_consentimento_cadastro.png` |
| Tela "Meus Dados" com histórico de consentimento | `lgpd_meus_dados.png` |
| Download do JSON exportado | `lgpd_exportacao.png` |
| Consentimento revogado (status na tela) | `lgpd_revogacao.png` |
| Confirmação de exclusão de dados | `lgpd_exclusao.png` |

---

# 8. Checklist dos requisitos

| Requisito | Implementação | Status |
|---|---|---|
| 4.1 — Listagem dos dados coletados | Seção 2 deste documento | Concluído |
| 4.2 — Associação dado × finalidade | Seção 2 deste documento | Concluído |
| 4.3 — Evidência de minimização | Seção 3 deste documento | Concluído |
| 4.4 — Registro explícito de consentimento | Checkbox obrigatório no cadastro + tabela `consentimentos` | Concluído |
| 4.5 — Consentimento associado à finalidade | Campo `finalidade` no registro de consentimento | Concluído |
| 4.6 — Revogação do consentimento | `LgpdController@revogarConsentimento` | Concluído |
| 4.7 — Data e versão do consentimento | Campos `aceito_em` e `versao_termo` | Concluído |
| 4.8 — Consulta aos dados do titular | Tela `/meus-dados` | Concluído |
| 4.9 — Exportação dos dados | `LgpdController@exportar` | Concluído |
| 4.10 — Exclusão dos dados pessoais | `LgpdController@excluir` (anonimização + soft delete) | Concluído |
| 4.11 — Fluxo documentado | Seção 5.4 deste documento | Concluído |

---

# 9. Considerações finais

A implementação do Requisito 4 formaliza, dentro do próprio sistema, os direitos que a LGPD garante ao titular dos dados — acesso, exportação, revogação e exclusão — sem depender de processos manuais externos. Combinada aos Requisitos 1 a 3 (autenticação, recuperação de senha e criptografia), a conformidade com a LGPD no Ecoa é tratada como parte da arquitetura do sistema, não como uma camada adicionada posteriormente.
