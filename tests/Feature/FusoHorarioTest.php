<?php

namespace Tests\Feature;

use App\Models\AuditoriaLog;
use App\Support\FusoHorario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Os horários são GRAVADOS em UTC e MOSTRADOS no fuso de Brasília.
 *
 * Os dois últimos testes existem para proteger a trilha de auditoria: o hash de
 * cada registro inclui o horário com o fuso, então a aplicação precisa continuar
 * em UTC. Se alguém trocar o fuso em config/app.php, estes testes falham antes
 * de a verificação de integridade começar a acusar "cadeia violada".
 */
class FusoHorarioTest extends TestCase
{
    use RefreshDatabase;

    public function test_horario_em_utc_aparece_no_fuso_de_brasilia(): void
    {
        $utc = Carbon::parse('2026-10-23 02:01:58', 'UTC');

        $this->assertSame('22/10/2026 23:01', FusoHorario::formatar($utc));
        $this->assertSame('22/10/2026 23:01:58', FusoHorario::formatar($utc, 'd/m/Y H:i:s'));
    }

    public function test_formatar_nao_altera_a_data_original(): void
    {
        $utc = Carbon::parse('2026-10-23 02:01:58', 'UTC');

        FusoHorario::formatar($utc);

        $this->assertSame('UTC', $utc->getTimezone()->getName());
        $this->assertSame('2026-10-23 02:01:58', $utc->format('Y-m-d H:i:s'));
    }

    public function test_data_ausente_mostra_o_texto_padrao(): void
    {
        $this->assertSame('—', FusoHorario::formatar(null));
        $this->assertSame('-', FusoHorario::formatar(null, 'd/m/Y', '-'));
        $this->assertSame('Não informado', FusoHorario::dataPorExtenso(null, 'Não informado'));
    }

    public function test_o_fuso_de_exibicao_vem_da_configuracao(): void
    {
        config(['ecoa.fuso_exibicao' => 'UTC']);

        $this->assertSame('23/10/2026 02:01', FusoHorario::formatar(Carbon::parse('2026-10-23 02:01:58', 'UTC')));
    }

    public function test_a_aplicacao_continua_gravando_em_utc(): void
    {
        $this->assertSame('UTC', config('app.timezone'));
    }

    public function test_a_cadeia_de_auditoria_continua_integra(): void
    {
        AuditoriaLog::registrar('login_sucesso', ['user_id' => null, 'email' => 'a@example.com']);
        AuditoriaLog::registrar('login_falha', ['email' => 'b@example.com']);

        $resultado = AuditoriaLog::verificarIntegridade();

        $this->assertNull($resultado['adulterado']);
        $this->assertSame(2, $resultado['total']);
    }
}
