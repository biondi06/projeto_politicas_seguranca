<?php

namespace Tests\Feature;

use App\Models\Consentimento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Garante que ninguém consegue escolher o próprio perfil no cadastro aberto.
 *
 * Antes da correção, o formulário enviava "perfil" e o servidor o aceitava, o
 * que permitia a qualquer visitante se cadastrar como Administrador de TI e
 * abrir a trilha de auditoria.
 */
class CadastroPerfilTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // As telas usam @vite; nos testes não há assets compilados.
        $this->withoutVite();
    }

    /** @param array<string, mixed> $extras */
    private function dadosDeCadastro(array $extras = []): array
    {
        return array_merge([
            'name' => 'Visitante',
            'email' => 'visitante@example.com',
            'password' => 'SenhaForte#2026',
            'password_confirmation' => 'SenhaForte#2026',
            'aceite_lgpd' => '1',
        ], $extras);
    }

    private function responsavelComConsentimento(): User
    {
        $usuario = User::factory()->create();
        $usuario->forceFill(['perfil' => 'responsavel_legal'])->save();

        Consentimento::create([
            'user_id' => $usuario->id,
            'finalidade' => 'Uso da plataforma Ecoa',
            'versao_termo' => 'v1.0',
            'aceito_em' => now(),
        ]);

        return $usuario;
    }

    public function test_cadastro_aberto_ignora_o_perfil_enviado_no_formulario(): void
    {
        $this->post('/register', $this->dadosDeCadastro(['perfil' => 'administrador_ti']))
            ->assertRedirect();

        $this->assertSame(
            'responsavel_legal',
            User::where('email', 'visitante@example.com')->value('perfil')
        );
    }

    public function test_cadastro_aberto_registra_o_consentimento_com_a_versao_do_termo(): void
    {
        $this->post('/register', $this->dadosDeCadastro())->assertRedirect();

        $usuario = User::where('email', 'visitante@example.com')->firstOrFail();
        $consentimento = Consentimento::where('user_id', $usuario->id)->firstOrFail();

        $this->assertSame(config('ecoa.termos_versao'), $consentimento->versao_termo);
        $this->assertNull($consentimento->revogado_em);
    }

    public function test_cadastro_exige_o_aceite_dos_termos(): void
    {
        $dados = $this->dadosDeCadastro();
        unset($dados['aceite_lgpd']);

        $this->post('/register', $dados)->assertSessionHasErrors('aceite_lgpd');

        $this->assertDatabaseMissing('users', ['email' => 'visitante@example.com']);
    }

    public function test_responsavel_legal_nao_acessa_a_auditoria(): void
    {
        $this->actingAs($this->responsavelComConsentimento())
            ->get('/auditoria')
            ->assertForbidden();
    }

    public function test_responsavel_legal_nao_acessa_a_gestao_de_convites(): void
    {
        $this->actingAs($this->responsavelComConsentimento())
            ->get('/administracao/convites')
            ->assertForbidden();
    }
}
