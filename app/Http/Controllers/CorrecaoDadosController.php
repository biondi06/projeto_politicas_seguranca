<?php

namespace App\Http\Controllers;

use App\Models\AuditoriaLog;
use App\Models\User;
use App\Notifications\EmailAlteradoNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

/**
 * Requisito 4 (LGPD): direito de correção dos dados (art. 18, III).
 *
 * O titular corrige sozinho o nome e o e-mail da própria conta, sem depender de
 * um pedido por e-mail. O princípio da qualidade dos dados (art. 6º, V) pede que
 * os dados sejam exatos e atualizados, e quem sabe se estão certos é o titular.
 *
 * Decisões de segurança:
 *  - O PERFIL de acesso não é editável aqui. Ele define permissões e só a
 *    administração o atribui; por isso o controller nunca lê "perfil" da requisição.
 *  - Mudar o e-mail muda como a pessoa entra e por onde recupera a senha, então
 *    exige a senha atual, além da confirmação de senha que a rota já pede.
 *  - Ao mudar o e-mail, o endereço ANTERIOR recebe um aviso. Se a conta tiver sido
 *    tomada por outra pessoa, o dono original fica sabendo.
 *  - A trilha de auditoria registra só QUAIS campos mudaram, nunca os valores,
 *    para não acrescentar dado pessoal à trilha (minimização, art. 6º, III).
 *
 * ATENÇÃO (AuditoriaLog::detalhes): a coluna é JSON e o MySQL reordena as chaves
 * de um objeto JSON. Como o hash da cadeia depende da ordem original, o evento
 * usa UMA única chave em "detalhes".
 */
class CorrecaoDadosController extends Controller
{
    public function edit(Request $request): View
    {
        return view('lgpd.corrigir-dados', ['user' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        // Usuário autenticado vindo da sessão (e não de um valor da requisição).
        $user = Auth::user();

        // Normaliza antes de validar: espaços repetidos viram um só, e o e-mail é
        // comparado em minúsculas (assim "Maria@x.com" e "maria@x.com" são o mesmo).
        $request->merge([
            'name' => preg_replace('/\s+/u', ' ', trim((string) $request->input('name'))),
            'email' => mb_strtolower(trim((string) $request->input('email'))),
        ]);

        $mudaEmail = $request->input('email') !== mb_strtolower($user->email);

        $dados = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255', 'regex:/^[^<>]+$/u'],
            'email' => ['required', 'email', 'max:255', Rule::unique(User::class, 'email')->ignore(Auth::id())],
            'senha_atual' => $mudaEmail ? ['required', 'string'] : ['nullable', 'string'],
        ], [
            'name.required' => 'Informe o seu nome.',
            'name.min' => 'O nome precisa ter pelo menos 2 caracteres.',
            'name.max' => 'O nome pode ter no máximo 255 caracteres.',
            'name.regex' => 'O nome não pode conter os caracteres < ou >.',
            'email.required' => 'Informe o seu e-mail.',
            'email.email' => 'Informe um e-mail válido.',
            'email.unique' => 'Este e-mail já está em uso em outra conta.',
            'senha_atual.required' => 'Para alterar o e-mail, informe a sua senha atual.',
        ]);

        // Conferência explícita da senha atual (em vez da regra pronta), para poder
        // limitar as tentativas na rota e para a mensagem ficar sob nosso controle.
        if ($mudaEmail && ! Hash::check($dados['senha_atual'], $user->password)) {
            throw ValidationException::withMessages([
                'senha_atual' => 'A senha atual informada está incorreta.',
            ]);
        }

        $campos = [];

        if ($dados['name'] !== $user->name) {
            $campos[] = 'nome';
        }

        if ($mudaEmail) {
            $campos[] = 'e-mail';
        }

        if ($campos === []) {
            return redirect()
                ->route('lgpd.corrigir')
                ->with('status', 'Nenhuma alteração: os dados informados já são os que constam no seu cadastro.')
                ->with('status_type', 'info');
        }

        $emailAnterior = $user->email;

        $user->name = $dados['name'];

        if ($mudaEmail) {
            $user->email = $dados['email'];
        }

        $user->save();

        AuditoriaLog::registrar('lgpd_dados_corrigidos', [
            'user_id' => $user->id,
            'detalhes' => ['campos' => implode(', ', $campos)],
        ]);

        if ($mudaEmail) {
            $this->avisarEmailAnterior($emailAnterior, $dados['email']);
        }

        return redirect()
            ->route('lgpd.meus-dados')
            ->with('status', 'Dados corrigidos com sucesso. A alteração foi registrada na trilha de auditoria.');
    }

    /**
     * Aviso de segurança ao endereço antigo. É uma proteção adicional: se o envio
     * falhar (e-mail não configurado, por exemplo), a correção já foi feita e o
     * erro apenas vai para o log.
     */
    private function avisarEmailAnterior(string $emailAnterior, string $emailNovo): void
    {
        try {
            Notification::route('mail', $emailAnterior)
                ->notify(new EmailAlteradoNotification($emailNovo));
        } catch (Throwable $erro) {
            report($erro);
        }
    }
}
