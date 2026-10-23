<?php

namespace App\Notifications;

use App\Models\ConviteAcesso;
use App\Support\FusoHorario;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * E-mail enviado à pessoa convidada, com o link para criar a conta.
 *
 * Usa o mesmo tema visual ("ecoa") e o mesmo "Responder para" do e-mail de
 * recuperação de senha. É enviado a um destinatário "avulso" (só o endereço),
 * porque a pessoa ainda não tem conta.
 */
class ConviteAcessoNotification extends Notification
{
    public function __construct(
        private readonly ConviteAcesso $convite,
        private readonly string $token,
        private readonly string $convidadoPor,
    ) {
    }

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $link = route('register', ['convite' => $this->token]);
        $perfil = $this->convite->perfil->rotulo();
        $validoAte = FusoHorario::formatar($this->convite->expira_em);

        return (new MailMessage)
            ->subject('Convite de acesso ao Ecoa')
            ->greeting('Olá!')
            ->line("{$this->convidadoPor} convidou você para criar uma conta no Ecoa com o perfil de {$perfil}.")
            ->action('Criar minha conta', $link)
            ->line("Este convite vale até {$validoAte}, só pode ser usado uma vez e só funciona com este e-mail.")
            ->line('Ao criar a conta, você escolhe a sua própria senha. Por segurança, no primeiro acesso você também ativa a verificação em duas etapas.')
            ->line('Se você não esperava este convite, pode ignorar esta mensagem.')
            ->salutation('Atenciosamente, Equipe Ecoa')
            ->theme('ecoa')
            ->withSymfonyMessage(function ($message) {
                $message->replyTo('fonoaudiologiaecoa@gmail.com');
            });
    }
}
