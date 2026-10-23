<?php

namespace App\Http\Controllers;

use App\Enums\Perfil;
use App\Models\AuditoriaLog;
use App\Models\ConviteAcesso;
use App\Models\User;
use App\Notifications\ConviteAcessoNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

/**
 * Gestão de acessos profissionais, exclusiva do Administrador de TI.
 *
 * É assim que contas de fonoaudiólogo, coordenador clínico e administrador
 * passam a existir: o administrador informa um e-mail e um perfil, o Ecoa envia
 * o convite por e-mail, e a própria pessoa cria a senha. O administrador nunca
 * vê nem define a senha de ninguém.
 */
class ConviteAcessoController extends Controller
{
    public function index(): View
    {
        Gate::authorize('gerenciar-acessos');

        return view('administracao.convites', [
            'convites' => ConviteAcesso::with('criador')->latest()->limit(50)->get(),
            'perfis' => Perfil::profissionais(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('gerenciar-acessos');

        $dados = $request->validate([
            'email' => ['required', 'email', 'max:255', Rule::unique(User::class, 'email')],
            'perfil' => ['required', Rule::enum(Perfil::class)->only(Perfil::profissionais())],
        ], [
            'email.required' => 'Informe o e-mail da pessoa que será convidada.',
            'email.email' => 'Informe um e-mail válido.',
            'email.unique' => 'Já existe uma conta com este e-mail.',
            'perfil.required' => 'Escolha o perfil de acesso.',
            'perfil.enum' => 'Escolha um dos perfis profissionais da lista.',
        ]);

        return $this->emitirEEnviar($request->user(), $dados['email'], Perfil::from($dados['perfil']));
    }

    /**
     * Reenvia um convite pendente.
     *
     * O token de um convite já criado só existe como hash e não pode ser lido
     * de volta. Por isso "reenviar" é: revogar o convite atual e emitir um novo,
     * igual, com um link novo.
     */
    public function reenviar(Request $request, ConviteAcesso $convite): RedirectResponse
    {
        Gate::authorize('gerenciar-acessos');

        if (! $convite->estaDisponivel()) {
            return redirect()
                ->route('convites.index')
                ->with('status', 'Só é possível reenviar convites pendentes.')
                ->with('status_type', 'danger');
        }

        $convite->forceFill(['revogado_em' => now()])->save();

        AuditoriaLog::registrar('convite_revogado', [
            'user_id' => $request->user()->id,
            'detalhes' => ['perfil' => $convite->perfil->value],
        ]);

        return $this->emitirEEnviar($request->user(), $convite->email, $convite->perfil);
    }

    public function destroy(ConviteAcesso $convite): RedirectResponse
    {
        Gate::authorize('gerenciar-acessos');

        if (! $convite->estaDisponivel()) {
            return redirect()
                ->route('convites.index')
                ->with('status', 'Este convite não está mais pendente.')
                ->with('status_type', 'danger');
        }

        $convite->forceFill(['revogado_em' => now()])->save();

        AuditoriaLog::registrar('convite_revogado', [
            'user_id' => auth()->id(),
            'detalhes' => ['perfil' => $convite->perfil->value],
        ]);

        return redirect()->route('convites.index')->with('status', 'Convite revogado.');
    }

    /**
     * Cria o convite e tenta entregá-lo por e-mail.
     *
     * Se o e-mail foi entregue de verdade, o administrador não vê o link: só
     * quem recebe a mensagem tem acesso a ele. Se o envio não é possível (o
     * ambiente não tem e-mail configurado, ou o envio falhou), o link aparece
     * uma única vez na tela, para o administrador entregá-lo por outro canal.
     * Assim o convite nunca fica "perdido".
     */
    private function emitirEEnviar(User $administrador, string $email, Perfil $perfil): RedirectResponse
    {
        [$convite, $token] = ConviteAcesso::emitir(
            $email,
            $perfil,
            $administrador,
            (int) config('ecoa.convite_validade_horas', 72),
        );

        AuditoriaLog::registrar('convite_criado', [
            'user_id' => $administrador->id,
            'detalhes' => ['perfil' => $perfil->value],
        ]);

        $resposta = redirect()->route('convites.index');

        if ($this->enviarPorEmail($convite, $token, $administrador)) {
            return $resposta
                ->with('status', "Convite enviado para {$convite->email}. Ele vale até {$convite->expira_em->format('d/m/Y H:i')}.")
                ->with('status_type', 'success');
        }

        return $resposta
            ->with('convite_link', route('register', ['convite' => $token]))
            ->with('convite_email', $convite->email)
            ->with('status', 'Não foi possível enviar o e-mail neste ambiente. Copie o link abaixo e envie à pessoa por outro canal.')
            ->with('status_type', 'info');
    }

    /**
     * Tenta enviar o convite por e-mail. Devolve true só se a mensagem saiu de
     * verdade: com o driver "log" ou "array" (usados em desenvolvimento e
     * testes) ela apenas é gravada, então não conta como entregue.
     */
    private function enviarPorEmail(ConviteAcesso $convite, string $token, User $administrador): bool
    {
        try {
            Notification::route('mail', $convite->email)
                ->notify(new ConviteAcessoNotification($convite, $token, $administrador->name));
        } catch (Throwable $erro) {
            // O problema é registrado no log do Laravel, mas não derruba a
            // tela: o administrador recebe o link como alternativa.
            report($erro);

            return false;
        }

        return ! in_array(config('mail.default'), ['log', 'array'], true);
    }
}
