# Compasso Livre

Portal de Educação Musical Inclusiva — projeto avaliativo do curso de Sistemas de Informação (6º semestre, 2026).

## Sobre o projeto

O Compasso Livre é um sistema que atua como elo entre professor de música, professor de AEE (Atendimento Educacional Especializado) e coordenação pedagógica, organizando planos de aula adaptados, materiais didáticos acessíveis e o acompanhamento do desenvolvimento de alunos com deficiência incluídos nas aulas de música.

## Funcionalidades (v1)

- Cadastro de alunos com Plano Educacional Individualizado (PEI) musical
- Biblioteca de materiais didáticos adaptados
- Plano de aula colaborativo (professor de música + professor de AEE)
- Registro de acompanhamento de progresso
- Painel do Coordenador Pedagógico

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
