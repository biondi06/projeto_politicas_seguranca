<?php

namespace Tests\Feature;

use App\Enums\Perfil;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CriaUsuarios;
use Tests\TestCase;

/**
 * Os atalhos de administração (Acessos profissionais e Auditoria) aparecem na
 * home só para quem tem permissão para usá-los.
 */
class AtalhosDeAdministracaoTest extends TestCase
{
    use RefreshDatabase;
    use CriaUsuarios;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    private function linkDeConvites(): string
    {
        return 'href="' . route('convites.index') . '"';
    }

    private function linkDeAuditoria(): string
    {
        return 'href="' . route('auditoria.index') . '"';
    }

    public function test_administrador_ve_os_dois_atalhos(): void
    {
        $this->actingAs($this->profissional(Perfil::AdministradorTi))
            ->get('/home')
            ->assertOk()
            ->assertSee($this->linkDeConvites(), false)
            ->assertSee($this->linkDeAuditoria(), false);
    }

    public function test_coordenador_ve_so_o_atalho_da_auditoria(): void
    {
        $this->actingAs($this->profissional(Perfil::CoordenadorClinico))
            ->get('/home')
            ->assertOk()
            ->assertSee($this->linkDeAuditoria(), false)
            ->assertDontSee($this->linkDeConvites(), false);
    }

    public function test_fonoaudiologo_nao_ve_atalhos_de_administracao(): void
    {
        $this->actingAs($this->profissional(Perfil::Fonoaudiologo))
            ->get('/home')
            ->assertOk()
            ->assertDontSee($this->linkDeConvites(), false)
            ->assertDontSee($this->linkDeAuditoria(), false);
    }

    public function test_responsavel_legal_nao_ve_atalhos_de_administracao(): void
    {
        $this->actingAs($this->responsavel())
            ->get('/home')
            ->assertOk()
            ->assertDontSee($this->linkDeConvites(), false)
            ->assertDontSee($this->linkDeAuditoria(), false);
    }
}
