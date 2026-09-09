<?php

namespace App\Providers;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Força HTTPS em produção
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Requisito 2.6 - registra solicitações de recuperação de senha
        Event::listen(function (\Illuminate\Notifications\Events\NotificationSending $event) {

            if ($event->notification instanceof ResetPassword) {

                Log::channel('single')->info(
                    'Solicitação de recuperação de senha enviada.',
                    [
                        'user_id' => $event->notifiable->id,
                        'email'   => $event->notifiable->email,
                        'ip'      => request()->ip(),
                    ]
                );
            }
        });

        // Requisito 2.7 - registra redefinições efetuadas
        Event::listen(function (PasswordReset $event) {

            Log::channel('single')->info(
                'Senha redefinida com sucesso.',
                [
                    'user_id' => $event->user->id,
                    'email'   => $event->user->email,
                    'ip'      => request()->ip(),
                ]
            );
        });

        // Personalização do e-mail de redefinição
        ResetPassword::toMailUsing(function ($notifiable, $token) {

            $url = route(
                'password.reset',
                [
                    'token' => $token,
                    'email' => $notifiable->getEmailForPasswordReset(),
                ],
                true
            );

            return (new MailMessage)
                ->subject('Redefinição de senha — Ecoa')
                ->greeting('Olá!')
                ->line('Recebemos uma solicitação para redefinir a senha da sua conta no Ecoa.')
                ->action('Redefinir senha', $url)
                ->line('Este link expira em 60 minutos.')
                ->line('Se você não solicitou essa alteração, nenhuma ação é necessária. Sua senha continua a mesma.')
                ->salutation('Atenciosamente, Equipe Ecoa')
                ->theme('ecoa');
        });
    }
}
