<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class TrailCommentNotification extends Notification
{
    use Queueable;

    protected $userName;
    protected $trailId;
    protected $commentBody;

    public function __construct($userName, $trailId, $commentBody)
    {
        $this->userName = $userName;
        $this->trailId = $trailId;
        $this->commentBody = $commentBody;
    }

    public function via($notifiable)
    {
        return ['database'];  // pode adicionar 'mail', 'broadcast' etc se quiser
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'comment',
            'user_name' => $this->userName,
            'trail_id' => $this->trailId,
            'comment_body' => $this->commentBody,
            'message' => "{$this->userName} comentou na sua trilha: \"{$this->commentBody}\"",
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Novo comentário na sua trilha')
            ->greeting("Olá!")
            ->line("{$this->userName} comentou na sua trilha:")
            ->line("\"{$this->commentBody}\"")
            ->action('Ver Trilha', url(route('trails.show', $this->trailId)))
            ->line('Obrigado por usar nosso aplicativo!');
    }
}
