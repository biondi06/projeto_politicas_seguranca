<?php

namespace App\Support;

use Carbon\CarbonInterface;

/**
 * Mostra horários no fuso de Brasília SEM mexer no que é gravado.
 *
 * O banco, o Laravel e a cadeia de hash da auditoria continuam em UTC. Isso é
 * proposital: o hash de cada registro de auditoria inclui o horário com o fuso,
 * então trocar o fuso da aplicação faria a verificação de integridade acusar
 * "cadeia violada" nos registros já gravados. Por isso a conversão acontece só
 * na hora de EXIBIR.
 *
 * O fuso de exibição fica em config/ecoa.php ('fuso_exibicao').
 */
class FusoHorario
{
    /** Ex.: 23/10/2026 02:01 (UTC) vira 22/10/2026 23:01. */
    public static function formatar(?CarbonInterface $data, string $formato = 'd/m/Y H:i', string $padrao = '—'): string
    {
        if ($data === null) {
            return $padrao;
        }

        // copy(): a data original não é alterada, só a cópia usada na tela.
        return $data->copy()->timezone(self::fuso())->format($formato);
    }

    /** Ex.: "22 de outubro de 2026" (com o mês no idioma configurado). */
    public static function dataPorExtenso(?CarbonInterface $data, string $padrao = '—'): string
    {
        if ($data === null) {
            return $padrao;
        }

        return $data->copy()->timezone(self::fuso())->translatedFormat('d \d\e F \d\e Y');
    }

    private static function fuso(): string
    {
        return (string) config('ecoa.fuso_exibicao', 'America/Sao_Paulo');
    }
}
