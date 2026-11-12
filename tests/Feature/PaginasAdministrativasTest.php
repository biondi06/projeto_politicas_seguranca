<?php

namespace Tests\Feature;

use App\Enums\Perfil;
use App\Models\AuditoriaLog;
use App\Models\ConviteAcesso;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CriaUsuarios;
use Tests\TestCase;

/**
 * As telas de administração e o cadastro foram redesenhados para o celular:
 * as tabelas viram cartões (atributos data-label) e a navegação ganha nomes curtos.
 * Estes testes garantem que as telas continuam abrindo e exibindo os dados.
 */
class PaginasAdministrativasTest extends TestCase
{
    use RefreshDatabase;
    use CriaUsuarios;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_auditoria_lista_os_eventos_com_rotulos_para_o_celular(): void
    {
        AuditoriaLog::registrar('login_falha', ['email' => 'alguem@example.com']);

        $this->actingAs($this->profissional(Perfil::AdministradorTi))
            ->get('/auditoria')
            ->assertOk()
            ->assertSee('Integridade da cadeia de logs')
            ->assertSee('data-label="Evento"', false)
            ->assertSee('Falha de login')
            ->assertSee('alguem@example.com');
    }

    public function test_auditoria_aceita_filtro_e_busca(): void
    {
        AuditoriaLog::registrar('login_falha', ['email' => 'alguem@example.com']);

        $this->actingAs($this->profissional(Perfil::CoordenadorClinico))
            ->get('/auditoria?filtro=lgpd&q=nao-existe')
            ->assertOk()
            ->assertSee('Nenhum evento encontrado para esse filtro.');
    }

    public function test_auditoria_usa_nomes_curtos_na_navegacao_do_celular(): void
    {
        $this->actingAs($this->profissional(Perfil::AdministradorTi))
            ->get('/auditoria')
            ->assertOk()
            ->assertSee('so-desktop', false);
    }

    public function test_acessos_profissionais_mostra_indicadores_e_cartoes(): void
    {
        $admin = $this->profissional(Perfil::AdministradorTi);
        ConviteAcesso::emitir('nova.pessoa@example.com', Perfil::Fonoaudiologo, $admin, 72);

        $this->actingAs($admin)
            ->get('/administracao/convites')
            ->assertOk()
            ->assertSee('pendente(s)')
            ->assertSee('data-label="Perfil"', false)
            ->assertSee('nova.pessoa@example.com');
    }

    public function test_acessos_profissionais_vazio_mostra_a_mensagem_de_lista_vazia(): void
    {
        $this->actingAs($this->profissional(Perfil::AdministradorTi))
            ->get('/administracao/convites')
            ->assertOk()
            ->assertSee('Nenhum convite emitido ainda.');
    }

    public function test_cadastro_rotula_a_tabela_de_dados_dos_termos_para_o_celular(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertSee('data-label="Base legal"', false)
            ->assertSee('data-label="Finalidade"', false);
    }
}
