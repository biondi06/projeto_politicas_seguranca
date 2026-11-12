<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Aviso de segurança enviado ao e-mail ANTERIOR quando o e-mail da conta muda.
 *
 * O novo endereço aparece mascarado (m***@dominio.com): o dono original reconhece
 * a alteração, ou estranha, sem que o aviso exponha o endereço inteiro.
 */
class EmailAlteradoNotification extends Notification
{
    public function __construct(private readonly string $emailNovo)
    {
    }

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('O e-mail da sua conta no Ecoa foi alterado')
            ->greeting('Olá!')
            ->line('O e-mail de acesso da sua conta no Ecoa foi alterado para ' . $this->mascarar($this->emailNovo) . '.')
            ->line('Se foi você quem fez a alteração, não precisa fazer nada.')
            ->line('Se você não reconhece essa alteração, escreva agora para fonoaudiologiaecoa@gmail.com, para que a administração proteja a conta.')
            ->salutation('Atenciosamente, Equipe Ecoa')
            ->theme('ecoa')
            ->withSymfonyMessage(function ($message) {
                $message->replyTo('fonoaudiologiaecoa@gmail.com');
            });
    }

    /** "maria@exemplo.com" vira "m***@exemplo.com". */
    private function mascarar(string $email): string
    {
        [$usuario, $dominio] = array_pad(explode('@', $email, 2), 2, '');

        return mb_substr($usuario, 0, 1) . '***@' . $dominio;
    }
}
