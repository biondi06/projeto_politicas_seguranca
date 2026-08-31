# Requisito 1 â€” AutenticaÃ§Ã£o e GestÃ£o de Credenciais

Este documento apresenta a implementaÃ§Ã£o do mÃ³dulo de **AutenticaÃ§Ã£o e GestÃ£o de Credenciais** do projeto **Ecoa**, desenvolvido com Laravel.

A documentaÃ§Ã£o aborda os requisitos de seguranÃ§a relacionados ao armazenamento de senhas, autenticaÃ§Ã£o em dois fatores, gerenciamento de sessÃµes, logout, proteÃ§Ã£o contra tentativas excessivas de login e as respectivas evidÃªncias de funcionamento.

O objetivo desta documentaÃ§Ã£o Ã© apresentar nÃ£o apenas as funcionalidades implementadas, mas tambÃ©m explicar as decisÃµes tÃ©cnicas utilizadas no desenvolvimento.

---

# SumÃ¡rio

1. [VisÃ£o geral e tecnologias utilizadas](#1-visÃ£o-geral-e-tecnologias-utilizadas)
2. [Fluxo de autenticaÃ§Ã£o](#2-fluxo-de-autenticaÃ§Ã£o)
3. [Hash e armazenamento de senhas](#3-hash-e-armazenamento-de-senhas)
4. [AutenticaÃ§Ã£o de dois fatores](#4-autenticaÃ§Ã£o-de-dois-fatores)
5. [Gerenciamento de sessÃµes e logout](#5-gerenciamento-de-sessÃµes-e-logout)
6. [ProteÃ§Ã£o contra forÃ§a bruta](#6-proteÃ§Ã£o-contra-forÃ§a-bruta)
7. [Justificativas tÃ©cnicas](#7-justificativas-tÃ©cnicas)
8. [EvidÃªncias de funcionamento](#8-evidÃªncias-de-funcionamento)
9. [Checklist dos requisitos](#9-checklist-dos-requisitos)
10. [ConsideraÃ§Ãµes finais](#10-consideraÃ§Ãµes-finais)

---

# 1. VisÃ£o geral e tecnologias utilizadas

O mÃ³dulo de autenticaÃ§Ã£o do Ecoa utiliza o **Laravel Fortify** como base para os recursos de autenticaÃ§Ã£o.

O Fortify foi utilizado porque fornece uma implementaÃ§Ã£o consolidada para funcionalidades como login, cadastro, logout, recuperaÃ§Ã£o de senha, autenticaÃ§Ã£o em dois fatores e controle de tentativas de autenticaÃ§Ã£o.

A interface do sistema foi desenvolvida separadamente, utilizando as views Blade do projeto. Dessa forma, o Fortify Ã© responsÃ¡vel principalmente pela lÃ³gica de autenticaÃ§Ã£o, enquanto as telas e a experiÃªncia de utilizaÃ§Ã£o foram desenvolvidas de acordo com a identidade visual do Ecoa.

## Tecnologias utilizadas

- Laravel 12
- PHP
- Laravel Fortify
- Blade
- Bootstrap
- Banco de dados relacional
- Argon2id para armazenamento das senhas
- TOTP para autenticaÃ§Ã£o em dois fatores

## Principais arquivos relacionados Ã  autenticaÃ§Ã£o

```text
app/Providers/FortifyServiceProvider.php

â†’ ConfiguraÃ§Ã£o dos serviÃ§os de autenticaÃ§Ã£o e Rate Limiters.

app/Actions/Fortify/CreateNewUser.php

â†’ ResponsÃ¡vel pela criaÃ§Ã£o de novos usuÃ¡rios.

app/Models/User.php

â†’ Model de usuÃ¡rio e configuraÃ§Ã£o relacionada ao 2FA.

app/Http/Controllers/SecurityController.php

â†’ ExibiÃ§Ã£o da Ã¡rea de seguranÃ§a da conta.

resources/views/auth/

â†’ Views de login, cadastro, recuperaÃ§Ã£o de senha e desafio 2FA.

resources/views/security.blade.php

â†’ Interface de gerenciamento da autenticaÃ§Ã£o em dois fatores.

config/fortify.php

â†’ ConfiguraÃ§Ã£o dos recursos disponibilizados pelo Fortify.

config/hashing.php

â†’ ConfiguraÃ§Ã£o do algoritmo utilizado para o hash das senhas.

config/session.php

â†’ ConfiguraÃ§Ã£o do tempo de duraÃ§Ã£o das sessÃµes.
```

---

# 2. Fluxo de autenticaÃ§Ã£o

O sistema possui diferentes etapas de autenticaÃ§Ã£o, dependendo da configuraÃ§Ã£o de seguranÃ§a da conta.

## 2.1 Cadastro

O cadastro ocorre da seguinte forma:

```text
UsuÃ¡rio
   |
   | Acessa /register
   v
Tela de cadastro
   |
   | Envia nome, e-mail e senha
   v
Laravel Fortify
   |
   | ValidaÃ§Ã£o dos dados
   | Hash da senha
   v
Banco de dados
   |
   | UsuÃ¡rio criado
   v
AutenticaÃ§Ã£o
   |
   v
Ãrea autenticada
```

Durante o cadastro, a senha informada pelo usuÃ¡rio nÃ£o Ã© armazenada diretamente no banco de dados.

Antes do armazenamento, ela passa pelo mecanismo de hashing configurado na aplicaÃ§Ã£o, utilizando o algoritmo Argon2id.

---

## 2.2 Login sem 2FA

Quando o usuÃ¡rio ainda nÃ£o possui autenticaÃ§Ã£o em dois fatores configurada, o fluxo ocorre da seguinte maneira:

```text
UsuÃ¡rio
   |
   | E-mail + senha
   v
Tela de login
   |
   v
Laravel Fortify
   |
   | VerificaÃ§Ã£o do Rate Limit
   |
   | Busca do usuÃ¡rio
   |
   | VerificaÃ§Ã£o da senha
   v
SessÃ£o autenticada
   |
   v
/home
```

Caso as credenciais estejam corretas e o usuÃ¡rio nÃ£o esteja sujeito a uma etapa adicional de autenticaÃ§Ã£o, o acesso Ã  Ã¡rea autenticada Ã© liberado.

---

## 2.3 Login com 2FA

Quando o usuÃ¡rio possui 2FA ativado e confirmado, o login possui uma etapa adicional:

```text
UsuÃ¡rio
   |
   | E-mail + senha
   v
Laravel Fortify
   |
   | Credenciais vÃ¡lidas
   v
VerificaÃ§Ã£o do 2FA
   |
   | CÃ³digo TOTP
   v
ValidaÃ§Ã£o do cÃ³digo
   |
   | CÃ³digo vÃ¡lido
   v
SessÃ£o autenticada
   |
   v
/home
```

Assim, possuir apenas a senha nÃ£o Ã© suficiente para concluir a autenticaÃ§Ã£o em uma conta protegida pelo segundo fator.

---

## 2.4 AtivaÃ§Ã£o do 2FA

A ativaÃ§Ã£o da autenticaÃ§Ã£o em dois fatores ocorre pela Ã¡rea de seguranÃ§a da aplicaÃ§Ã£o.

O processo Ã©:

1. O usuÃ¡rio acessa a Ã¡rea de seguranÃ§a.
2. O sistema solicita uma nova confirmaÃ§Ã£o da senha.
3. O usuÃ¡rio confirma sua identidade.
4. O sistema gera o segredo utilizado pelo TOTP.
5. Um QR Code Ã© disponibilizado para configuraÃ§Ã£o no aplicativo autenticador.
6. O usuÃ¡rio informa o cÃ³digo gerado pelo aplicativo.
7. O sistema valida o cÃ³digo.
8. ApÃ³s a confirmaÃ§Ã£o, o 2FA passa a fazer parte do processo de login.

O sistema tambÃ©m disponibiliza cÃ³digos de recuperaÃ§Ã£o para situaÃ§Ãµes em que o usuÃ¡rio nÃ£o tenha acesso ao aplicativo autenticador.

---

## 2.5 Logout

O logout encerra a sessÃ£o autenticada do usuÃ¡rio.

O processo Ã© realizado pelo Laravel Fortify por meio da rota de logout disponibilizada pela autenticaÃ§Ã£o.

O fluxo esperado Ã©:

```text
UsuÃ¡rio
   |
   | Solicita logout
   v
Laravel Fortify
   |
   | Encerra autenticaÃ§Ã£o
   | Invalida sessÃ£o
   | Regenera token CSRF
   v
UsuÃ¡rio nÃ£o autenticado
   |
   v
PÃ¡gina pÃºblica
```

A rota utilizada pelo projeto Ã©:

```text
POST /logout
```

Essa rota Ã© fornecida pelo `AuthenticatedSessionController` do Laravel Fortify.

---

# 3. Hash e armazenamento de senhas

## 3.1 Algoritmo utilizado

O Ecoa utiliza **Argon2id** para o armazenamento seguro das senhas.

A configuraÃ§Ã£o Ã© definida em:

```text
config/hashing.php
```

A aplicaÃ§Ã£o utiliza o driver `argon2id` para realizar o hashing das senhas:

```php
'driver' => env('HASH_DRIVER', 'argon2id'),
```

O objetivo do hashing Ã© impedir que a senha original precise ser armazenada no banco de dados.

Dessa forma, a senha do usuÃ¡rio nÃ£o fica disponÃ­vel em texto puro no banco de dados.

---

## 3.2 ParÃ¢metros de custo

O Argon2id possui parÃ¢metros que determinam o custo computacional utilizado durante a geraÃ§Ã£o e verificaÃ§Ã£o do hash.

No ambiente de desenvolvimento, os parÃ¢metros configurados foram verificados atravÃ©s do Laravel Tinker:

```text
memory  = 65536
threads = 1
time    = 4
verify  = true
```

Esses valores representam:

| ParÃ¢metro | Valor | FunÃ§Ã£o |
|---|---:|---|
| `memory` | 65536 KB | Quantidade de memÃ³ria utilizada |
| `threads` | 1 | Paralelismo utilizado |
| `time` | 4 | Custo de tempo/iteraÃ§Ãµes |
| `verify` | true | VerificaÃ§Ã£o dos parÃ¢metros do hash durante a operaÃ§Ã£o |

A representaÃ§Ã£o do hash observada no ambiente tambÃ©m apresenta:

```text
m=65536,t=4,p=1
```

Os parÃ¢metros aumentam o custo necessÃ¡rio para realizar grandes quantidades de tentativas de descoberta de senhas.

Os valores devem ser avaliados de acordo com o ambiente de execuÃ§Ã£o do projeto, pois o desempenho pode variar conforme o hardware disponÃ­vel.

---

## 3.3 Salt criptogrÃ¡fico

O Argon2id utiliza um **salt aleatÃ³rio** durante a geraÃ§Ã£o do hash.

Esse salt Ã© gerado automaticamente pelo mecanismo de hashing e nÃ£o precisa ser criado manualmente pela aplicaÃ§Ã£o.

Consequentemente, duas contas que utilizem a mesma senha podem possuir hashes diferentes.

Isso dificulta ataques baseados na comparaÃ§Ã£o direta de hashes e no uso de tabelas prÃ©-computadas.

---

## 3.4 Armazenamento do hash e salt

O salt nÃ£o precisa ser armazenado em uma coluna separada.

O padrÃ£o utilizado pelo Argon2id armazena as informaÃ§Ãµes necessÃ¡rias dentro da prÃ³pria representaÃ§Ã£o do hash.

Um hash Argon2id possui uma estrutura semelhante a:

```text
$argon2id$v=19$m=65536,t=4,p=1$SALT$HASH
```

Nesse formato estÃ£o presentes:

| Parte | FunÃ§Ã£o |
|---|---|
| `argon2id` | Algoritmo utilizado |
| `v=19` | VersÃ£o do algoritmo |
| `m=65536` | MemÃ³ria utilizada |
| `t=4` | Custo de tempo |
| `p=1` | Paralelismo |
| `SALT` | Salt utilizado |
| `HASH` | Resultado do processo de hashing |

Essas informaÃ§Ãµes fazem parte da representaÃ§Ã£o armazenada no campo de senha do usuÃ¡rio.

A aplicaÃ§Ã£o nÃ£o precisa armazenar a senha original para realizar a autenticaÃ§Ã£o.

---

# 4. AutenticaÃ§Ã£o de dois fatores

## 4.1 ImplementaÃ§Ã£o

O Ecoa utiliza **TOTP (Time-based One-Time Password)** para implementar a autenticaÃ§Ã£o em dois fatores.

O TOTP gera cÃ³digos temporÃ¡rios que podem ser utilizados por aplicativos autenticadores.

O processo de ativaÃ§Ã£o Ã©:

```text
UsuÃ¡rio
   |
   | Solicita ativaÃ§Ã£o
   v
Sistema gera segredo
   |
   v
QR Code
   |
   v
Aplicativo autenticador
   |
   | Gera cÃ³digo temporÃ¡rio
   v
UsuÃ¡rio informa cÃ³digo
   |
   v
Sistema valida cÃ³digo
   |
   v
2FA confirmado
```

O segredo utilizado pelo 2FA Ã© armazenado de forma protegida pelo mecanismo utilizado pelo Fortify.

AlÃ©m disso, o sistema possui cÃ³digos de recuperaÃ§Ã£o para permitir o acesso Ã  conta em situaÃ§Ãµes especÃ­ficas.

---

## 4.2 ValidaÃ§Ã£o apÃ³s a autenticaÃ§Ã£o primÃ¡ria

A autenticaÃ§Ã£o em dois fatores adiciona uma segunda etapa ao processo de login.

Primeiramente, o sistema verifica:

```text
E-mail
+
Senha
```

ApÃ³s a validaÃ§Ã£o dessas informaÃ§Ãµes, caso o usuÃ¡rio possua 2FA confirmado, o sistema solicita:

```text
CÃ³digo TOTP
```

Somente apÃ³s a validaÃ§Ã£o do segundo fator o processo de autenticaÃ§Ã£o Ã© concluÃ­do.

Isso reduz o impacto de uma eventual exposiÃ§Ã£o da senha, pois a senha isoladamente nÃ£o Ã© suficiente para concluir o acesso Ã  conta protegida pelo 2FA.

---

# 5. Gerenciamento de sessÃµes e logout

## 5.1 DuraÃ§Ã£o da sessÃ£o

A duraÃ§Ã£o da sessÃ£o Ã© configurada em:

```text
config/session.php
```

Atualmente, o projeto estÃ¡ configurado com:

```php
'lifetime' => (int) env('SESSION_LIFETIME', 1),

'expire_on_close' => env('SESSION_EXPIRE_ON_CLOSE', false),
```

O valor padrÃ£o utilizado pelo projeto Ã© de **1 minuto de inatividade**.

Essa configuraÃ§Ã£o foi utilizada para facilitar a demonstraÃ§Ã£o e os testes de expiraÃ§Ã£o da sessÃ£o durante a etapa de desenvolvimento.

O valor pode ser alterado por meio da variÃ¡vel:

```text
SESSION_LIFETIME
```

---

## 5.2 Armazenamento das sessÃµes

O projeto utiliza o driver de sessÃ£o:

```php
'driver' => env('SESSION_DRIVER', 'database'),
```

Dessa forma, quando a variÃ¡vel de ambiente nÃ£o sobrescreve a configuraÃ§Ã£o, as sessÃµes sÃ£o armazenadas utilizando o banco de dados.

A tabela utilizada Ã©:

```text
sessions
```

---

## 5.3 InvalidaÃ§Ã£o da sessÃ£o no logout

Ao realizar logout, o Laravel encerra a autenticaÃ§Ã£o do usuÃ¡rio e invalida a sessÃ£o correspondente.

O mecanismo utilizado pelo framework realiza operaÃ§Ãµes equivalentes a:

```php
Auth::guard('web')->logout();

$request->session()->invalidate();

$request->session()->regenerateToken();
```

A invalidaÃ§Ã£o da sessÃ£o impede que a sessÃ£o anterior continue sendo utilizada normalmente apÃ³s o logout.

A regeneraÃ§Ã£o do token contribui para a proteÃ§Ã£o das requisiÃ§Ãµes posteriores contra reutilizaÃ§Ã£o do token CSRF anterior.

---

# 6. ProteÃ§Ã£o contra forÃ§a bruta

O sistema possui **Rate Limiting** para limitar a quantidade de tentativas realizadas em operaÃ§Ãµes de autenticaÃ§Ã£o.

A configuraÃ§Ã£o Ã© realizada em:

```text
app/Providers/FortifyServiceProvider.php
```

---

## 6.1 Login

O limite configurado para login Ã© de:

```text
5 tentativas por minuto
```

O controle utiliza uma chave formada pelo identificador utilizado no login e pelo endereÃ§o IP da tentativa.

A implementaÃ§Ã£o utilizada Ã©:

```php
RateLimiter::for('login', function (Request $request) {

    $throttleKey = Str::transliterate(
        Str::lower($request->input(Fortify::username()))
        .'|'.$request->ip()
    );

    return Limit::perMinute(5)->by($throttleKey);
});
```

Com isso, uma quantidade excessiva de tentativas dentro do perÃ­odo configurado Ã© temporariamente limitada.

---

## 6.2 Two-Factor

O processo de validaÃ§Ã£o do 2FA tambÃ©m possui limitaÃ§Ã£o de tentativas.

A implementaÃ§Ã£o utilizada Ã©:

```php
RateLimiter::for('two-factor', function (Request $request) {

    return Limit::perMinute(5)
        ->by($request->session()->get('login.id'));

});
```

Dessa maneira, tambÃ©m existe uma barreira contra tentativas repetidas de descoberta do cÃ³digo de autenticaÃ§Ã£o.

---

# 7. Justificativas tÃ©cnicas

## 7.1 Escolha do Argon2id

O Argon2id foi escolhido por ser um algoritmo moderno de hashing de senhas e por utilizar recursos de memÃ³ria e processamento.

Esse comportamento aumenta o custo de ataques que tentem testar grandes quantidades de senhas.

Outro ponto importante Ã© que o mecanismo possui suporte integrado ao PHP e ao Laravel, permitindo sua utilizaÃ§Ã£o sem a necessidade de desenvolver um algoritmo prÃ³prio de seguranÃ§a.

---

## 7.2 Escolha dos parÃ¢metros

Os parÃ¢metros de memÃ³ria, tempo e paralelismo foram configurados para aumentar o custo das operaÃ§Ãµes de hashing sem tornar o processo excessivamente lento para a utilizaÃ§Ã£o normal do sistema.

No ambiente utilizado para desenvolvimento, foram configurados:

```text
memory = 65536 KB
threads = 1
time = 4
verify = true
```

A configuraÃ§Ã£o deve buscar um equilÃ­brio entre **seguranÃ§a e desempenho**, considerando as caracterÃ­sticas do ambiente de execuÃ§Ã£o.

---

## 7.3 Escolha do TOTP

O TOTP foi utilizado para adicionar uma segunda camada de autenticaÃ§Ã£o sem depender do envio de cÃ³digos por SMS.

O cÃ³digo Ã© gerado por um aplicativo autenticador e possui validade temporÃ¡ria.

ApÃ³s a configuraÃ§Ã£o inicial, o mecanismo tambÃ©m pode gerar cÃ³digos sem depender de conexÃ£o com a rede mÃ³vel.

---

## 7.4 Escolha do Rate Limiting

O Rate Limiting foi utilizado para reduzir a quantidade de tentativas que um atacante pode realizar em operaÃ§Ãµes de autenticaÃ§Ã£o.

Sem essa proteÃ§Ã£o, um atacante poderia realizar uma grande quantidade de tentativas de senha em um curto perÃ­odo.

Com a limitaÃ§Ã£o configurada, o nÃºmero de tentativas permitidas Ã© reduzido, dificultando ataques automatizados de forÃ§a bruta.

---

# 8. EvidÃªncias de funcionamento

As funcionalidades sÃ£o comprovadas por meio de testes realizados atravÃ©s do **front-end da aplicaÃ§Ã£o**, conforme solicitado na avaliaÃ§Ã£o do Projeto Integrador.

As imagens estÃ£o armazenadas no diretÃ³rio:

```text
docs/evidencias/
```

---

## 8.1 Cadastro

![Evidência do cadastro](evidencias/cadastro.png)

Arquivo:

```text
docs/evidencias/cadastro.png
```

A evidÃªncia demonstra a utilizaÃ§Ã£o da tela de cadastro e o processo de criaÃ§Ã£o de usuÃ¡rio.

---

## 8.2 Login

![Evidência do login](evidencias/login_realizado.png)

Arquivo:

```text
docs/evidencias/login_realizado.png
```

A evidÃªncia demonstra a utilizaÃ§Ã£o de credenciais vÃ¡lidas e o acesso Ã  Ã¡rea autenticada.

---

## 8.3 Hash da senha e seguranÃ§a das credenciais

Arquivo:

```text
docs/evidencias/seguranca_credenciais_argon2id_2fa.png
```

A evidÃªncia demonstra aspectos relacionados Ã  proteÃ§Ã£o das credenciais e Ã  utilizaÃ§Ã£o do Argon2id e da autenticaÃ§Ã£o em dois fatores.

A configuraÃ§Ã£o tÃ©cnica do hash tambÃ©m foi validada atravÃ©s do Laravel Tinker, confirmando a utilizaÃ§Ã£o do driver `argon2id` e seus parÃ¢metros.

A senha original nÃ£o Ã© armazenada em texto puro.

---

## 8.4 AtivaÃ§Ã£o do 2FA

Arquivo:

```text
docs/evidencias/ativar_verificacao.png
```

A evidÃªncia demonstra o acesso Ã  funcionalidade de ativaÃ§Ã£o da autenticaÃ§Ã£o em dois fatores.

---

## 8.5 QR Code do 2FA

![Evidência do QR Code](evidencias/fa_qrcode.png)

Arquivo:

```text
docs/evidencias/fa_qrcode.png
```

A evidÃªncia demonstra a geraÃ§Ã£o e apresentaÃ§Ã£o do QR Code utilizado para configurar o aplicativo autenticador.

---

## 8.6 ConfirmaÃ§Ã£o do 2FA

Arquivo:

```text
docs/evidencias/fa_confirmado.png
```

A evidÃªncia demonstra a confirmaÃ§Ã£o do segundo fator apÃ³s a validaÃ§Ã£o do cÃ³digo fornecido pelo aplicativo autenticador.

---

## 8.7 AutenticaÃ§Ã£o

Arquivo:

```text
docs/evidencias/autenticacao.png
```

A evidÃªncia demonstra o processo de autenticaÃ§Ã£o realizado pela aplicaÃ§Ã£o.

---

## 8.8 ProteÃ§Ã£o contra forÃ§a bruta

Arquivos:

```text
docs/evidencias/rate_limit.png
docs/evidencias/tela_limit.png
```

As evidÃªncias demonstram o comportamento da aplicaÃ§Ã£o apÃ³s tentativas consecutivas de autenticaÃ§Ã£o.

ApÃ³s atingir o limite configurado, novas tentativas sÃ£o temporariamente limitadas.

---

## 8.9 Gerenciamento de sessÃ£o

Arquivos:

```text
docs/evidencias/sessao_validada.png
docs/evidencias/sessao_invalidada.png
```

As evidÃªncias demonstram o comportamento da aplicaÃ§Ã£o em relaÃ§Ã£o Ã  validaÃ§Ã£o e invalidaÃ§Ã£o da sessÃ£o autenticada.

A configuraÃ§Ã£o de `SESSION_LIFETIME` tambÃ©m permite testar a expiraÃ§Ã£o da sessÃ£o apÃ³s o perÃ­odo de inatividade configurado.

---

# 9. Checklist dos requisitos

| Requisito | ImplementaÃ§Ã£o | EvidÃªncia |
|---|---|---|
| 1.1 â€” Hash seguro | Argon2id | `seguranca_credenciais_argon2id_2fa.png` |
| 1.2 â€” ParÃ¢metros de custo | MemÃ³ria, tempo e paralelismo configurados | `seguranca_credenciais_argon2id_2fa.png` |
| 1.3 â€” Salt Ãºnico | Gerado automaticamente pelo Argon2id | `seguranca_credenciais_argon2id_2fa.png` |
| 1.4 â€” Armazenamento seguro | Hash armazenado no campo de senha | `seguranca_credenciais_argon2id_2fa.png` |
| 1.5 â€” 2FA implementado | TOTP atravÃ©s do Fortify | `fa_qrcode.png` |
| 1.6 â€” ValidaÃ§Ã£o do 2FA | CÃ³digo validado apÃ³s a senha | `fa_confirmado.png` / `autenticacao.png` |
| 1.7 â€” Fluxo de autenticaÃ§Ã£o | Cadastro, login, 2FA e logout | EvidÃªncias do diretÃ³rio |
| 1.8 â€” EvidÃªncias | Capturas dos testes funcionais | `docs/evidencias/` |
| 1.9 â€” DuraÃ§Ã£o da sessÃ£o | `SESSION_LIFETIME` configurado | `sessao_validada.png` / `sessao_invalidada.png` |
| 1.10 â€” InvalidaÃ§Ã£o no logout | SessÃ£o invalidada | `sessao_invalidada.png` |
| 1.11 â€” ProteÃ§Ã£o contra forÃ§a bruta | Rate Limiting | `rate_limit.png` / `tela_limit.png` |
| 1.12 â€” Justificativas tÃ©cnicas | DecisÃµes documentadas | Este documento |

---

# 10. ConsideraÃ§Ãµes finais

A primeira etapa do mÃ³dulo de seguranÃ§a do Ecoa concentra-se na proteÃ§Ã£o da autenticaÃ§Ã£o e das credenciais dos usuÃ¡rios.

A utilizaÃ§Ã£o do Laravel Fortify permite aproveitar mecanismos consolidados de autenticaÃ§Ã£o, enquanto as configuraÃ§Ãµes realizadas no projeto definem os parÃ¢metros necessÃ¡rios para atender aos requisitos de seguranÃ§a.

Foram considerados mecanismos para proteÃ§Ã£o de senhas, autenticaÃ§Ã£o em dois fatores, gerenciamento de sessÃµes, logout e limitaÃ§Ã£o de tentativas.

A utilizaÃ§Ã£o do **Argon2id** garante que as senhas sejam armazenadas por meio de hashing, evitando seu armazenamento em texto puro. O mecanismo tambÃ©m utiliza salt e parÃ¢metros de custo que aumentam a dificuldade de ataques automatizados.

A autenticaÃ§Ã£o em dois fatores utilizando **TOTP** adiciona uma camada adicional de proteÃ§Ã£o Ã s contas dos usuÃ¡rios.

O gerenciamento de sessÃµes utiliza o armazenamento em banco de dados e possui tempo de expiraÃ§Ã£o configurÃ¡vel. O logout tambÃ©m encerra a autenticaÃ§Ã£o e invalida a sessÃ£o.

O mecanismo de **Rate Limiting** reduz a quantidade de tentativas permitidas em operaÃ§Ãµes de autenticaÃ§Ã£o, contribuindo para a proteÃ§Ã£o contra ataques de forÃ§a bruta.

AlÃ©m da implementaÃ§Ã£o, os testes realizados atravÃ©s da interface da aplicaÃ§Ã£o foram registrados em forma de evidÃªncias e disponibilizados no diretÃ³rio:

```text
docs/evidencias/
```

A documentaÃ§Ã£o, o cÃ³digo-fonte e as evidÃªncias sÃ£o mantidos junto ao projeto no GitHub, permitindo que a implementaÃ§Ã£o do mÃ³dulo seja analisada durante a avaliaÃ§Ã£o.
