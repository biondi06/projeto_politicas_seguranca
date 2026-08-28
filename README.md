# Ecoa
Sistema de Acompanhamento Fonoaudiológico Infantil — projeto avaliativo do curso de Sistemas de Informação (6º semestre, 2026).
## Sobre o projeto
O Ecoa é um sistema que atua como elo entre o fonoaudiólogo, demais profissionais envolvidos no caso (como pediatra e terapeuta ocupacional) e a coordenação clínica, organizando planos terapêuticos fonoaudiológicos, materiais de estimulação da fala e o acompanhamento da evolução de crianças em terapia de desenvolvimento da fala.
## Funcionalidades (v1)
- Cadastro de crianças com Plano Terapêutico Fonoaudiológico
- Biblioteca de exercícios de estimulação da fala
- Plano terapêutico colaborativo (entre fonoaudiólogos)
- Diário de evolução da fala
- Painel do Coordenador Clínico
## Stack
- **Backend:** Laravel (arquitetura MVC)
- **Frontend:** Bootstrap (última versão) + jQuery
- **Banco de dados:** MySQL
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
