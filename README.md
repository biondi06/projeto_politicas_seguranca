##Ecoa

Sistema de Acompanhamento Fonoaudiológico Infantil — projeto avaliativo do curso de Sistemas de Informação (6º semestre, 2026).

Sobre o projeto##

O Ecoa é um sistema que atua como elo entre o fonoaudiólogo, demais profissionais envolvidos no caso (como pediatra e terapeuta ocupacional) e a coordenação clínica, organizando planos terapêuticos fonoaudiológicos, materiais de estimulação da fala e o acompanhamento da evolução de crianças em terapia de desenvolvimento da fala (atraso de fala, trocas fonéticas, gagueira, entre outros).

Funcionalidades (v1)
Cadastro de crianças com Plano Terapêutico Fonoaudiológico
Biblioteca de exercícios de estimulação da fala
Plano terapêutico colaborativo (entre fonoaudiólogos)
Diário de evolução da fala
Painel do Coordenador Clínico
Perfis de acesso
Fonoaudiólogo: cria e edita o plano terapêutico, acessa a biblioteca de exercícios e registra a evolução apenas dos seus pacientes.
Coordenador Clínico: acesso de leitura a todos os planos e registros, gera relatórios consolidados.
Administrador de TI: gerencia contas, permissões e parâmetros técnicos; não acessa dados clínicos.
Responsável Legal: fornece os dados da criança e tem acesso aos exercícios e relatórios do fonoaudiólogo.
LGPD

O sistema trata dados pessoais sensíveis de saúde (Art. 5º, II da LGPD), como o plano terapêutico e o diário de evolução da fala. Por isso, conta com controle de acesso por perfil, minimização de dados e restrição de acesso do Administrador de TI aos dados clínicos, em conformidade com o Art. 14 da LGPD (tratamento de dados de crianças e adolescentes).

Stack
Backend: Laravel (arquitetura MVC)
Frontend: Bootstrap (última versão) + jQuery
Banco de dados: MySQL

```bash
composer install
npm install
php artisan key:generate
php artisan migrate
php artisan serve
npm run build
```

Acesse em `http://127.0.0.1:8000`
