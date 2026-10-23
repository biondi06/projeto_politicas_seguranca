<?php

namespace App\Http\Controllers;

use App\Models\AuditoriaLog;
use App\Models\Consentimento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Requisito 4 (LGPD) — direitos do titular, exercidos pelo próprio usuário.
 *
 * Cada ação relevante também é gravada na trilha de auditoria (Requisito 5),
 * para que fique comprovado quando e o que o titular fez.
 *
 * ATENÇÃO (AuditoriaLog::detalhes): a coluna é JSON e o MySQL reordena as
 * chaves de um objeto JSON. Como o hash da cadeia depende da ordem original,
 * os eventos abaixo usam no máximo UMA chave em "detalhes". Com duas ou mais,
 * a verificação de integridade poderia acusar adulteração sem ter havido.
 */
class LgpdController extends Controller
{
    private const FINALIDADE = 'Uso da plataforma Ecoa e tratamento de dados terapêuticos da criança acompanhada';

    /** Versão do texto dos termos (a mesma gravada no cadastro). */
    private function versaoTermo(): string
    {
        return config('ecoa.termos_versao', 'v1.0');
    }

    // 4.8 — Consulta aos dados do titular
    public function meusDados(Request $request)
    {
        $user = $request->user();

        $consentimentos = Consentimento::where('user_id', $user->id)
            ->orderByDesc('aceito_em')
            ->get();

        return view('lgpd.meus-dados', [
            'user' => $user,
            'consentimentos' => $consentimentos,
            'consentimentoAtivo' => $consentimentos->first(fn ($c) => $c->revogado_em === null),
            'versaoTermo' => $this->versaoTermo(),
        ]);
    }

    // 4.9 — Exportação dos dados (portabilidade) em arquivo de texto (.txt)
    public function exportar(Request $request)
    {
        $user = $request->user();

        $formata = fn ($data) => \App\Support\FusoHorario::formatar($data, 'd/m/Y H:i:s', '-');

        $consentimentos = Consentimento::where('user_id', $user->id)
            ->orderBy('aceito_em')
            ->get();

        $linha = str_repeat('-', 60);

        $linhas = [];
        $linhas[] = 'ECOA — EXPORTAÇÃO DE DADOS PESSOAIS';
        $linhas[] = 'Gerado em: ' . \App\Support\FusoHorario::formatar(now(), 'd/m/Y H:i:s') . ' (horário de Brasília)';
        $linhas[] = str_repeat('=', 60);
        $linhas[] = '';

        $linhas[] = 'DADOS DA CONTA';
        $linhas[] = $linha;
        $linhas[] = 'Nome: ' . $user->name;
        $linhas[] = 'E-mail: ' . $user->email;
        $linhas[] = 'Perfil de acesso: ' . ($user->perfil ?? 'não definido');
        $linhas[] = 'Cadastrado em: ' . $formata($user->created_at);
        $linhas[] = 'Verificação em duas etapas: ' . ($user->two_factor_confirmed_at ? 'ativa' : 'inativa');
        $linhas[] = '';

        $linhas[] = 'CONSENTIMENTOS';
        $linhas[] = $linha;
        if ($consentimentos->isEmpty()) {
            $linhas[] = 'Nenhum consentimento registrado.';
            $linhas[] = '';
        }
        foreach ($consentimentos as $i => $c) {
            $linhas[] = ($i + 1) . '. Finalidade: ' . $c->finalidade;
            $linhas[] = '   Versão do termo: ' . $c->versao_termo;
            $linhas[] = '   Concedido em: ' . $formata($c->aceito_em);
            $linhas[] = '   Revogado em: ' . $formata($c->revogado_em);
            $linhas[] = '   Endereço IP: ' . ($c->ip ?? '-');
            $linhas[] = '';
        }

        $linhas[] = str_repeat('=', 60);
        $linhas[] = 'Senhas e segredos de autenticação nunca fazem parte desta exportação.';

        // Registra a exportação na trilha de auditoria (requisito 5)
        AuditoriaLog::registrar('lgpd_exportacao', ['user_id' => $user->id]);

        // BOM UTF-8 + quebras de linha CRLF: o Bloco de Notas do Windows
        // mostra os acentos e as linhas corretamente.
        $conteudo = "\xEF\xBB\xBF" . implode("\r\n", $linhas) . "\r\n";

        return response($conteudo, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="meus-dados-ecoa-' . \App\Support\FusoHorario::formatar(now(), 'Ymd-His') . '.txt"',
        ]);
    }

    // 4.4 / 4.7 — Novo consentimento (nunca reativa o registro revogado)
    public function consentir(Request $request)
    {
        $user = $request->user();

        if (Consentimento::where('user_id', $user->id)->whereNull('revogado_em')->exists()) {
            return redirect()->route('lgpd.meus-dados')->with('status', 'Você já possui um consentimento ativo.');
        }

        Consentimento::create([
            'user_id' => $user->id,
            'finalidade' => self::FINALIDADE,
            'versao_termo' => $this->versaoTermo(),
            'aceito_em' => now(),
            'ip' => $request->ip(),
        ]);

        AuditoriaLog::registrar('lgpd_consentimento_concedido', [
            'user_id' => $user->id,
            'detalhes' => ['versao_termo' => $this->versaoTermo()],
        ]);

        return redirect()->route('lgpd.meus-dados')->with('status', 'Consentimento registrado. O acesso ao sistema foi restabelecido.');
    }

    // 4.6 — Revogação do consentimento (o histórico é preservado)
    public function revogarConsentimento(Request $request)
    {
        $user = $request->user();

        $revogados = Consentimento::where('user_id', $user->id)
            ->whereNull('revogado_em')
            ->update(['revogado_em' => now()]);

        if ($revogados > 0) {
            AuditoriaLog::registrar('lgpd_consentimento_revogado', ['user_id' => $user->id]);
        }

        return redirect()->route('lgpd.meus-dados')->with('status', 'Consentimento revogado. O acesso às áreas do sistema fica bloqueado até um novo consentimento.');
    }

    // 4.10 — Exclusão dos dados pessoais (anonimização + exclusão lógica)
    public function excluir(Request $request)
    {
        // A exclusão é irreversível: exige a senha atual.
        $request->validate(
            ['password' => ['required', 'current_password']],
            [
                'password.required' => 'Informe a sua senha para confirmar a exclusão.',
                'password.current_password' => 'A senha informada está incorreta.',
            ]
        );

        $user = $request->user();
        $id = $user->id;

        // Registrado antes de anonimizar, só com o ID (sem e-mail), para não
        // acrescentar dado pessoal à trilha de auditoria.
        AuditoriaLog::registrar('lgpd_exclusao', ['user_id' => $id]);

        DB::transaction(function () use ($user, $id) {
            // Consentimentos: encerra os ativos e remove o IP. A data e a versão
            // continuam, porque comprovam que o consentimento existiu.
            Consentimento::where('user_id', $id)->whereNull('revogado_em')->update(['revogado_em' => now()]);
            Consentimento::where('user_id', $id)->update(['ip' => null]);

            $user->forceFill([
                'name' => 'Usuário removido',
                'email' => 'removido+' . $id . '@ecoa.invalid',
                'password' => Hash::make(Str::random(64)),
                'remember_token' => null,
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
                'two_factor_confirmed_at' => null,
            ])->save();

            $user->delete(); // exclusão lógica (SoftDeletes)
        });

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'Seus dados pessoais foram anonimizados e a sua conta foi encerrada.');
    }
}
