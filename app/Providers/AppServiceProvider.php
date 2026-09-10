<?php

namespace App\Providers;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Requisito 2.6 — registra toda solicitação de recuperação de senha.
        Event::listen(function (\Illuminate\Notifications\Events\NotificationSending $event) {
            if ($event->notification instanceof ResetPassword) {
                Log::channel('single')->info('Solicitação de recuperação de senha enviada.', [
                    'user_id' => $event->notifiable->id,
                    'email' => $event->notifiable->email,
                    'ip' => request()->ip(),
                ]);
            }
        });

        // Requisito 2.7 — registra quando a senha é redefinida com sucesso.
        Event::listen(function (PasswordReset $event) {
            Log::channel('single')->info('Senha redefinida com sucesso.', [
                'user_id' => $event->user->id,
                'email' => $event->user->email,
                'ip' => request()->ip(),
            ]);
        });

        // Personaliza o e-mail de recuperação de senha (texto em
        // português + identidade visual do Ecoa via tema "ecoa.css").
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
                ->theme('ecoa');
        });
    }
}