<?php

/*
|--------------------------------------------------------------------------
| Configurações próprias do Ecoa
|--------------------------------------------------------------------------
|
| Os valores vêm do ambiente (.env local, ou as variáveis do painel da
| hospedagem). Ler variáveis de ambiente só aqui é uma regra do Laravel:
| em produção, depois de `config:cache`, a função env() fora dos arquivos de
| config devolve nulo.
*/

return [

    // Versão do texto dos Termos de Uso exibido no cadastro. É a mesma versão
    // que fica gravada em cada consentimento.
    'termos_versao' => env('ECOA_TERMOS_VERSAO', 'v1.0'),

    // Fuso em que os horários são MOSTRADOS ao usuário. O banco e a cadeia de
    // hash da auditoria continuam em UTC (config/app.php); ver App\Support\FusoHorario.
    'fuso_exibicao' => env('ECOA_FUSO_EXIBICAO', 'America/Sao_Paulo'),

    // Por quantas horas um convite de acesso profissional continua válido.
    'convite_validade_horas' => (int) env('ECOA_CONVITE_VALIDADE_HORAS', 72),

    // Primeiro administrador do sistema, criado por
    // `php artisan db:seed --class=AdministradorInicialSeeder`.
    // A senha NUNCA deve ser escrita no repositório: só em variável de ambiente.
    'admin_inicial' => [
        'nome' => env('ADMIN_INICIAL_NOME', 'Administrador de TI'),
        'email' => env('ADMIN_INICIAL_EMAIL'),
        'senha' => env('ADMIN_INICIAL_SENHA'),
    ],

];
