<?php

namespace Tests\Feature;

use App\Enums\Perfil;
use App\Models\ConviteAcesso;
use App\Models\User;
use App\Notifications\ConviteAcessoNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\Concerns\CriaUsuarios;
use Tests\TestCase;

/**
 * Fluxo completo de convites: como contas profissionais passam a existir.
 */
class ConviteAcessoTest extends TestCase
{
    use RefreshDatabase;
    use CriaUsuarios;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    /** @param array<string, mixed> $extras */
    private function cadastroComConvite(string $token, string $email, array $extras = []): array
    {
        return array_merge([
            'name' => 'Pessoa Convidada',
            'email' => $email,
            'password' => 'SenhaForte#2026',
            'password_confirmation' => 'SenhaForte#2026',
            'aceite_lgpd' => '1',
            'convite' => $token,
        ], $extras);
    }

    // ---------------------------------------------------------------
    // Cadastro com convite
    // ---------------------------------------------------------------

    public function test_cadastro_com_convite_cria_a_conta_com_o_perfil_do_convite(): void
    {
        $admin = $this->profissional(Perfil::AdministradorTi);
        [$convite, $token] = ConviteAcesso::emitir('fono@example.com', Perfil::Fonoaudiologo, $admin, 72);

        $this->post('/register', $this->cadastroComConvite($token, 'fono@example.com'))
            ->assertRedirect();

        $novo = User::where('email', 'fono@example.com')->firstOrFail();

        $this->assertSame('fonoaudiologo', $novo->perfil);
        $this->assertSame('usado', $convite->fresh()->situacao());
        $this->assertEquals($novo->id, $convite->fresh()->usado_por);
    }

    public function test_o_perfil_vem_do_convite_mesmo_que_o_formulario_envie_outro(): void
    {
        $admin = $this->profissional(Perfil::AdministradorTi);
        [, $token] = ConviteAcesso::emitir('fono@example.com', Perfil::Fonoaudiologo, $admin, 72);

        $this->post('/register', $this->cadastroComConvite($token, 'fono@example.com', [
            'perfil' => 'administrador_ti',
        ]))->assertRedirect();

        $this->assertSame('fonoaudiologo', User::where('email', 'fono@example.com')->value('perfil'));
    }

    public function test_convite_ja_usado_e_recusado(): void
    {
        $admin = $this->profissional(Perfil::AdministradorTi);
        [$convite, $token] = ConviteAcesso::emitir('fono@example.com', Perfil::Fonoaudiologo, $admin, 72);
        $convite->forceFill(['usado_em' => now()])->save();

        $this->post('/register', $this->cadastroComConvite($token, 'fono@example.com'))
            ->assertSessionHasErrors('convite');

        $this->assertDatabaseMissing('users', ['email' => 'fono@example.com']);
    }

    public function test_convite_expirado_e_recusado(): void
    {
        $admin = $this->profissional(Perfil::AdministradorTi);
        [$convite, $token] = ConviteAcesso::emitir('fono@example.com', Perfil::Fonoaudiologo, $admin, 72);
        $convite->forceFill(['expira_em' => now()->subMinute()])->save();

        $this->post('/register', $this->cadastroComConvite($token, 'fono@example.com'))
            ->assertSessionHasErrors('convite');

        $this->assertDatabaseMissing('users', ['email' => 'fono@example.com']);
    }

    public function test_convite_revogado_e_recusado(): void
    {
        $admin = $this->profissional(Perfil::AdministradorTi);
        [$convite, $token] = ConviteAcesso::emitir('fono@example.com', Perfil::Fonoaudiologo, $admin, 72);
        $convite->forceFill(['revogado_em' => now()])->save();

        $this->post('/register', $this->cadastroComConvite($token, 'fono@example.com'))
            ->assertSessionHasErrors('convite');

        $this->assertDatabaseMissing('users', ['email' => 'fono@example.com']);
    }

    public function test_convite_vale_apenas_para_o_email_convidado(): void
    {
        $admin = $this->profissional(Perfil::AdministradorTi);
        [, $token] = ConviteAcesso::emitir('fono@example.com', Perfil::Fonoaudiologo, $admin, 72);

        $this->post('/register', $this->cadastroComConvite($token, 'outra.pessoa@example.com'))
            ->assertSessionHasErrors('convite');

        $this->assertDatabaseMissing('users', ['email' => 'outra.pessoa@example.com']);
    }

    public function test_token_inventado_e_recusado(): void
    {
        $this->post('/register', $this->cadastroComConvite('token-que-nao-existe', 'qualquer@example.com'))
            ->assertSessionHasErrors('convite');

        $this->assertDatabaseMissing('users', ['email' => 'qualquer@example.com']);
    }

    // ---------------------------------------------------------------
    // Quem pode gerenciar convites
    // ---------------------------------------------------------------

    public function test_so_o_administrador_acessa_a_gestao_de_convites(): void
    {
        $this->actingAs($this->profissional(Perfil::CoordenadorClinico))
            ->get('/administracao/convites')
            ->assertForbidden();

        $this->actingAs($this->profissional(Perfil::AdministradorTi))
            ->get('/administracao/convites')
            ->assertOk();
    }

    public function test_administrador_nao_emite_convite_para_responsavel_legal(): void
    {
        Notification::fake();

        $this->actingAs($this->profissional(Perfil::AdministradorTi))
            ->post('/administracao/convites', [
                'email' => 'responsavel@example.com',
                'perfil' => 'responsavel_legal',
            ])
            ->assertSessionHasErrors('perfil');

        $this->assertDatabaseCount('convites_acesso', 0);
        Notification::assertNothingSent();
    }

    public function test_nao_se_emite_convite_para_email_que_ja_tem_conta(): void
    {
        Notification::fake();

        $admin = $this->profissional(Perfil::AdministradorTi);
        User::factory()->create(['email' => 'ja.existe@example.com']);

        $this->actingAs($admin)
            ->post('/administracao/convites', [
                'email' => 'ja.existe@example.com',
                'perfil' => 'fonoaudiologo',
            ])
            ->assertSessionHasErrors('email');

        Notification::assertNothingSent();
    }

    public function test_administrador_revoga_um_convite_pendente(): void
    {
        $admin = $this->profissional(Perfil::AdministradorTi);
        [$convite] = ConviteAcesso::emitir('fono@example.com', Perfil::Fonoaudiologo, $admin, 72);

        $this->actingAs($admin)
            ->delete(route('convites.destroy', $convite))
            ->assertRedirect(route('convites.index'));

        $this->assertSame('revogado', $convite->fresh()->situacao());
    }

    // ---------------------------------------------------------------
    // Envio do convite por e-mail
    // ---------------------------------------------------------------

    public function test_convite_e_enviado_por_email_e_o_administrador_nao_ve_o_link(): void
    {
        Notification::fake();
        config(['mail.default' => 'smtp']); // simula um ambiente com e-mail de verdade

        $this->actingAs($this->profissional(Perfil::AdministradorTi))
            ->post('/administracao/convites', [
                'email' => 'coordenacao@example.com',
                'perfil' => 'coordenador_clinico',
            ])
            ->assertRedirect(route('convites.index'))
            ->assertSessionMissing('convite_link');

        Notification::assertSentOnDemand(
            ConviteAcessoNotification::class,
            fn ($notificacao, $canais, $notificavel) => $notificavel->routes['mail'] === 'coordenacao@example.com'
        );
    }

    public function test_sem_envio_real_de_email_o_link_e_mostrado_ao_administrador_uma_unica_vez(): void
    {
        Notification::fake();
        config(['mail.default' => 'log']); // ambiente de desenvolvimento: o e-mail só é gravado

        $admin = $this->profissional(Perfil::AdministradorTi);

        $this->actingAs($admin)
            ->post('/administracao/convites', [
                'email' => 'coordenacao@example.com',
                'perfil' => 'coordenador_clinico',
            ])
            ->assertRedirect(route('convites.index'))
            ->assertSessionHas('convite_link');

        // No banco fica só o hash: o token em texto puro nunca é gravado.
        parse_str((string) parse_url(session('convite_link'), PHP_URL_QUERY), $consulta);
        $token = $consulta['convite'];

        $this->assertDatabaseMissing('convites_acesso', ['token_hash' => $token]);
        $this->assertDatabaseHas('convites_acesso', [
            'token_hash' => ConviteAcesso::hashDoToken($token),
            'email' => 'coordenacao@example.com',
            'perfil' => 'coordenador_clinico',
        ]);

        // A primeira visita à lista mostra o link; a segunda, não.
        $this->get('/administracao/convites')->assertSee('Link do convite para');
        $this->get('/administracao/convites')->assertDontSee('Link do convite para');
    }

    public function test_o_email_do_convite_traz_o_link_o_perfil_e_o_nome_de_quem_convidou(): void
    {
        $admin = $this->profissional(Perfil::AdministradorTi);
        [$convite, $token] = ConviteAcesso::emitir('fono@example.com', Perfil::Fonoaudiologo, $admin, 72);

        $mensagem = (new ConviteAcessoNotification($convite, $token, 'Ana Administradora'))
            ->toMail((object) []);

        $this->assertStringContainsString($token, $mensagem->actionUrl);
        $this->assertStringContainsString('Ana Administradora', $mensagem->introLines[0]);
        $this->assertStringContainsString('Fonoaudiólogo', $mensagem->introLines[0]);
    }

    // ---------------------------------------------------------------
    // Reenvio
    // ---------------------------------------------------------------

    public function test_reenviar_revoga_o_convite_antigo_e_emite_um_novo(): void
    {
        Notification::fake();
        config(['mail.default' => 'smtp']);

        $admin = $this->profissional(Perfil::AdministradorTi);
        [$antigo] = ConviteAcesso::emitir('fono@example.com', Perfil::Fonoaudiologo, $admin, 72);

        $this->actingAs($admin)
            ->post(route('convites.reenviar', $antigo))
            ->assertRedirect(route('convites.index'));

        $novo = ConviteAcesso::where('id', '!=', $antigo->id)->firstOrFail();

        $this->assertSame('revogado', $antigo->fresh()->situacao());
        $this->assertSame('pendente', $novo->situacao());
        $this->assertSame('fono@example.com', $novo->email);
        $this->assertSame(Perfil::Fonoaudiologo, $novo->perfil);

        Notification::assertSentOnDemand(
            ConviteAcessoNotification::class,
            fn ($notificacao, $canais, $notificavel) => $notificavel->routes['mail'] === 'fono@example.com'
        );
    }

    public function test_nao_se_reenvia_convite_que_nao_esta_pendente(): void
    {
        Notification::fake();

        $admin = $this->profissional(Perfil::AdministradorTi);
        [$convite] = ConviteAcesso::emitir('fono@example.com', Perfil::Fonoaudiologo, $admin, 72);
        $convite->forceFill(['usado_em' => now()])->save();

        $this->actingAs($admin)
            ->post(route('convites.reenviar', $convite))
            ->assertRedirect(route('convites.index'));

        $this->assertDatabaseCount('convites_acesso', 1);
        Notification::assertNothingSent();
    }

    public function test_so_o_administrador_reenvia_convites(): void
    {
        $admin = $this->profissional(Perfil::AdministradorTi);
        [$convite] = ConviteAcesso::emitir('fono@example.com', Perfil::Fonoaudiologo, $admin, 72);

        $this->actingAs($this->profissional(Perfil::CoordenadorClinico))
            ->post(route('convites.reenviar', $convite))
            ->assertForbidden();

        $this->assertSame('pendente', $convite->fresh()->situacao());
    }
}
