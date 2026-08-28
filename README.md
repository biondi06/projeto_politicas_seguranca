# Ecoa
Sistema de Acompanhamento Fonoaudiológico Infantil — projeto avaliativo do curso de Sistemas de Informação (6º semestre, 2026).
## Sobre o projeto
O Ecoa é um sistema que atua como elo entre o fonoaudiólogo, demais profissionais envolvidos no caso (como pediatra e terapeuta ocupacional) e a coordenação clínica, organizando planos terapêuticos fonoaudiológicos, materiais de estimulação da fala e o acompanhamento da evolução de crianças em terapia de desenvolvimento da fala.
Problema real que o sistema resolve: 
Crianças em acompanhamento de desenvolvimento da fala (atraso de fala, trocas fonéticas, gagueira, 
entre outros) costumam ser atendidas por um fonoaudiólogo que trabalha isolado da escola, do pediatra e 
de outros especialistas envolvidos no caso, porque: 

- Não há canal estruturado para o fonoaudiólogo compartilhar o plano terapêutico com pediatra e 
terapeuta ocupacional que acompanham a mesma criança;

- Falta material padronizado de estimulação da fala que os pais possam reproduzir corretamente em 
casa (hoje isso costuma ser passado em papel avulso ou verbalmente); 

- O acompanhamento do progresso da criança é feito de forma informal (anotações soltas), sem 
histórico estruturado que ajude a ajustar a terapia ao longo do tempo;
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
