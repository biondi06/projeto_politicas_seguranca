<?php

namespace Tests\Feature;

use App\Enums\Perfil;
use App\Models\AuditoriaLog;
use App\Models\Consentimento;
use App\Models\User;
use App\Notifications\EmailAlteradoNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\Concerns\CriaUsuarios;
use Tests\TestCase;

/**
 * Direito de correção do titular (LGPD, art. 18, III): o usuário corrige o próprio
 * nome e e-mail, com as proteções que a troca de e-mail exige.
 */
class CorrecaoDadosTest extends TestCase
{
    use RefreshDatabase;
    use CriaUsuarios;

    private const SENHA = 'SenhaAtual#2026';

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    /** Usuário com senha conhecida e com a senha já confirmada na sessão (rota password.confirm). */
    private function logado(?User $usuario = null): User
    {
        $usuario ??= $this->responsavel();
        $usuario->forceFill(['password' => Hash::make(self::SENHA)])->save();

        $this->actingAs($usuario)->withSession(['auth.password_confirmed_at' => time()]);

        return $usuario;
    }

    public function test_visitante_e_enviado_para_o_login(): void
    {
        $this->get('/meus-dados/corrigir')->assertRedirect('/login');
    }

    public function test_a_tela_de_correcao_abre_com_os_dados_atuais(): void
    {
        $usuario = $this->logado();

        $this->get('/meus-dados/corrigir')
            ->assertOk()
            ->assertSee('Corrigir meus dados')
            ->assertSee($usuario->email);
    }

    public function test_meus_dados_oferece_o_caminho_para_a_correcao(): void
    {
        $this->logado();

        $this->get('/meus-dados')
            ->assertOk()
            ->assertSee(route('lgpd.corrigir'));
    }

    public function test_titular_corrige_o_proprio_nome_sem_precisar_da_senha(): void
    {
        $usuario = $this->logado();

        $this->patch('/meus-dados/corrigir', ['name' => 'Maria da Silva Santos', 'email' => $usuario->email])
            ->assertRedirect(route('lgpd.meus-dados'))
            ->assertSessionHas('status');

        $this->assertSame('Maria da Silva Santos', $usuario->fresh()->name);
    }

    public function test_a_correcao_entra_na_trilha_sem_guardar_os_valores(): void
    {
        $usuario = $this->logado();

        $this->patch('/meus-dados/corrigir', ['name' => 'Maria da Silva Santos', 'email' => $usuario->email]);

        $log = AuditoriaLog::where('evento', 'lgpd_dados_corrigidos')->firstOrFail();

        $this->assertSame($usuario->id, $log->user_id);
        $this->assertSame('nome', $log->detalhes['campos']);
        $this->assertNull($log->email);
        $this->assertStringNotContainsString('Maria', json_encode($log->detalhes));
    }

    public function test_a_cadeia_de_auditoria_continua_integra_apos_a_correcao(): void
    {
        $usuario = $this->logado();

        $this->patch('/meus-dados/corrigir', ['name' => 'Maria da Silva Santos', 'email' => $usuario->email]);

        $this->assertNull(AuditoriaLog::verificarIntegridade()['adulterado']);
    }

    public function test_espacos_repetidos_no_nome_sao_normalizados(): void
    {
        $usuario = $this->logado();

        $this->patch('/meus-dados/corrigir', ['name' => "  Ana    Maria \t Souza  ", 'email' => $usuario->email]);

        $this->assertSame('Ana Maria Souza', $usuario->fresh()->name);
    }

    public function test_nome_com_marcacao_html_e_recusado(): void
    {
        $usuario = $this->logado();
        $nomeAntes = $usuario->name;

        $this->patch('/meus-dados/corrigir', ['name' => '<script>alert(1)</script>', 'email' => $usuario->email])
            ->assertSessionHasErrors('name');

        $this->assertSame($nomeAntes, $usuario->fresh()->name);
    }

    public function test_trocar_o_email_exige_a_senha_atual(): void
    {
        $usuario = $this->logado();
        $emailAntes = $usuario->email;

        $this->patch('/meus-dados/corrigir', ['name' => $usuario->name, 'email' => 'novo@example.com'])
            ->assertSessionHasErrors('senha_atual');

        $this->assertSame($emailAntes, $usuario->fresh()->email);
    }

    public function test_senha_atual_incorreta_nao_troca_o_email(): void
    {
        $usuario = $this->logado();
        $emailAntes = $usuario->email;

        $this->patch('/meus-dados/corrigir', [
            'name' => $usuario->name,
            'email' => 'novo@example.com',
            'senha_atual' => 'senha-errada-123',
        ])->assertSessionHasErrors('senha_atual');

        $this->assertSame($emailAntes, $usuario->fresh()->email);
    }

    public function test_troca_de_email_com_a_senha_certa_avisa_o_endereco_anterior(): void
    {
        Notification::fake();

        $usuario = $this->responsavel();
        $usuario->forceFill(['email' => 'antigo@example.com'])->save();
        $this->logado($usuario);

        $this->patch('/meus-dados/corrigir', [
            'name' => $usuario->name,
            'email' => 'NOVO@Example.com',
            'senha_atual' => self::SENHA,
        ])->assertRedirect(route('lgpd.meus-dados'));

        $this->assertSame('novo@example.com', $usuario->fresh()->email);

        Notification::assertSentOnDemand(
            EmailAlteradoNotification::class,
            fn ($notificacao, $canais, $notificavel) => $notificavel->routes['mail'] === 'antigo@example.com'
        );

        $this->assertSame('e-mail', AuditoriaLog::where('evento', 'lgpd_dados_corrigidos')->firstOrFail()->detalhes['campos']);
    }

    public function test_nome_e_email_juntos_registram_os_dois_campos(): void
    {
        Notification::fake();

        $usuario = $this->logado();

        $this->patch('/meus-dados/corrigir', [
            'name' => 'Nome Corrigido',
            'email' => 'corrigido@example.com',
            'senha_atual' => self::SENHA,
        ]);

        $this->assertSame('nome, e-mail', AuditoriaLog::where('evento', 'lgpd_dados_corrigidos')->firstOrFail()->detalhes['campos']);
        $this->assertSame('Nome Corrigido', $usuario->fresh()->name);
    }

    public function test_email_ja_usado_por_outra_conta_e_recusado(): void
    {
        User::factory()->create(['email' => 'ocupado@example.com']);

        $usuario = $this->logado();

        $this->patch('/meus-dados/corrigir', [
            'name' => $usuario->name,
            'email' => 'ocupado@example.com',
            'senha_atual' => self::SENHA,
        ])->assertSessionHasErrors('email');
    }

    public function test_mudar_so_maiusculas_do_email_nao_conta_como_alteracao(): void
    {
        $usuario = $this->responsavel();
        $usuario->forceFill(['email' => 'maria@example.com'])->save();
        $this->logado($usuario);

        $this->patch('/meus-dados/corrigir', ['name' => $usuario->name, 'email' => 'MARIA@EXAMPLE.COM'])
            ->assertRedirect(route('lgpd.corrigir'))
            ->assertSessionHas('status_type', 'info');

        $this->assertDatabaseCount('logs_auditorias', 0);
    }

    public function test_o_perfil_nao_pode_ser_alterado_pela_requisicao(): void
    {
        $usuario = $this->logado();

        $this->patch('/meus-dados/corrigir', [
            'name' => 'Nome Novo',
            'email' => $usuario->email,
            'perfil' => Perfil::AdministradorTi->value,
        ]);

        $this->assertSame(Perfil::ResponsavelLegal->value, $usuario->fresh()->perfil);
    }

    public function test_quem_revogou_o_consentimento_ainda_pode_corrigir_os_dados(): void
    {
        $usuario = $this->logado();

        Consentimento::where('user_id', $usuario->id)->update(['revogado_em' => now()]);

        // Corrigir dados é um direito do titular e não depende de consentimento ativo.
        $this->get('/meus-dados/corrigir')->assertOk();
    }

    public function test_o_aviso_ao_email_anterior_mascara_o_novo_endereco(): void
    {
        $mensagem = (new EmailAlteradoNotification('maria@exemplo.com'))->toMail((object) []);

        $this->assertStringContainsString('m***@exemplo.com', $mensagem->introLines[0]);
        $this->assertStringNotContainsString('maria@exemplo.com', $mensagem->introLines[0]);
    }
}
