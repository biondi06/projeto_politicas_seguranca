<?php

namespace App\Support;

/**
 * Traduz os códigos de evento da trilha de auditoria (ex.: "login_falha")
 * para um texto legível e define a cor do selo. Usado pela página
 * "Meus dados" e pelo painel de auditoria, para os dois mostrarem os
 * eventos do mesmo jeito.
 */
class EventoAuditoria
{
    private const ROTULOS = [
        'login_sucesso'                   => 'Login realizado',
        'login_falha'                     => 'Falha de login',
        '2fa_falha'                       => 'Falha na verificação em duas etapas',
        '2fa_reiniciado_segredo_invalido' => '2FA reiniciado (segredo inválido)',
        'lgpd_consentimento_concedido'    => 'Consentimento concedido',
        'lgpd_consentimento_revogado'     => 'Consentimento revogado',
        'lgpd_exportacao'                 => 'Exportação de dados',
        'lgpd_exclusao'                   => 'Exclusão de dados',
    ];

    public static function rotulo(string $evento): string
    {
        return self::ROTULOS[$evento] ?? $evento;
    }

    /** Classe do selo: success, danger, warning ou neutral. */
    public static function classe(string $evento): string
    {
        return match (true) {
            in_array($evento, ['login_sucesso', 'lgpd_consentimento_concedido'], true) => 'success',
            str_contains($evento, 'falha') => 'danger',
            in_array($evento, ['lgpd_consentimento_revogado', 'lgpd_exclusao', '2fa_reiniciado_segredo_invalido'], true) => 'warning',
            default => 'neutral',
        };
    }
}
