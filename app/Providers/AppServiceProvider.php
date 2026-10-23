<?php

namespace App\Providers;

use App\Enums\Perfil;
use App\Models\AuditoriaLog;
use App\Models\ConviteAcesso;
use App\Models\User;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Requisito 3: em produção, todos os links gerados usam HTTPS.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        $this->definirAutorizacoes();
        $this->entregarConviteAoCadastro();
        $this->registrarEventosDeSeguranca();
        $this->personalizarEmailDeRecuperacao();
    }

    /**
     * Quem pode o quê, definido num único lugar.
     *
     * Antes, a lista de perfis autorizados era repetida dentro de cada
     * controller. Agora uma mudança de regra acontece só aqui.
     */
    private function definirAutorizacoes(): void
    {
        // Trilha de auditoria: coordenador clínico e administrador de TI (RN-18).
        Gate::define('ver-auditoria', fn (User $usuario) => in_array(
            $usuario->perfil,
            [Perfil::CoordenadorClinico->value, Perfil::AdministradorTi->value],
            true
        ));

        // Convites de acesso profissional: somente o administrador de TI.
        Gate::define('gerenciar-acessos', fn (User $usuario) => $usuario->perfil === Perfil::AdministradorTi->value);
    }

    /**
     * A tela de cadastro recebe o convite válido do link (?convite=...), ou
     * null no cadastro aberto. Assim a tela mostra o perfil convidado e trava o
     * e-mail, sem ter regra de negócio dentro do Blade.
     */
    private function entregarConviteAoCadastro(): void
    {
        View::composer('auth.register', function ($view) {
            $view->with('convite', ConviteAcesso::validoPorToken(request('convite')));
        });
    }

    /**
     * Requisitos 2.6, 2.7, 5.1 e 5.2: registro dos eventos de segurança.
     */
    private function registrarEventosDeSeguranca(): void
    {
        // 2.6 — toda solicitação de recuperação de senha.
        Event::listen(function (\Illuminate\Notifications\Events\NotificationSending $event) {
            if ($event->notification instanceof ResetPassword) {
                Log::channel('single')->info('Solicitação de recuperação de senha enviada.', [
                    'user_id' => $event->notifiable->id,
                    'email' => $event->notifiable->email,
                    'ip' => request()->ip(),
                ]);
            }
        });

        // 2.7 — senha redefinida com sucesso.
        Event::listen(function (PasswordReset $event) {
            Log::channel('single')->info('Senha redefinida com sucesso.', [
                'user_id' => $event->user->id,
                'email' => $event->user->email,
                'ip' => request()->ip(),
            ]);
        });

        // 5.1 — login bem-sucedido (inclui após o 2FA, já que o Fortify só
        // dispara Login quando a autenticação está completa).
        Event::listen(function (Login $event) {
            AuditoriaLog::registrar('login_sucesso', [
                'user_id' => $event->user->id,
                'email' => $event->user->email,
            ]);
        });

        // 5.2 — tentativa de login malsucedida.
        Event::listen(function (Failed $event) {
            AuditoriaLog::registrar('login_falha', [
                'email' => $event->credentials[Fortify::username()] ?? null,
            ]);
        });
    }

    /**
     * E-mail de recuperação de senha: texto em português, identidade visual do
     * Ecoa (tema "ecoa") e "Responder para" apontando para o e-mail oficial do
     * projeto, de modo que qualquer resposta caia na caixa real do Ecoa.
     */
    private function personalizarEmailDeRecuperacao(): void
    {
        ResetPassword::toMailUsing(function ($notifiable, $token) {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new MailMessage)
                ->subject('Redefinição de senha — Ecoa')
                ->greeting('Olá!')
                ->line('Recebemos uma solicitação para redefinir a senha da sua conta no Ecoa.')
                ->action('Redefinir senha', $url)
                ->line('Este link expira em 60 minutos.')
                ->line('Se você não solicitou essa alteração, nenhuma ação é necessária — sua senha continua a mesma.')
                ->salutation('Atenciosamente, Equipe Ecoa')
                ->theme('ecoa')
                ->withSymfonyMessage(function ($message) {
                    $message->replyTo('fonoaudiologiaecoa@gmail.com');
                });
        });
    }
}