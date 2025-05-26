<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TrailInteractionNotification extends Notification
{
    use Queueable;

    protected $type;
    protected $data;

    public function __construct(string $type, array $data = [])
    {
        $this->type = $type;
        $this->data = $data;
    }

    // Notifica **somente via database**
    public function via($notifiable)
    {
        return ['database'];
    }

    // Dados que serão armazenados na tabela notifications (coluna data)
    public function toArray($notifiable)
    {
        return [
            'type' => $this->type,
            'user_name' => $this->data['user_name'] ?? null,
            'trail_id' => $this->data['trail_id'] ?? null,
            'comment_id' => $this->data['comment_id'] ?? null,
            'message' => $this->buildMessage(),
        ];
    }

    protected function buildMessage()
    {
        $userName = $this->data['user_name'] ?? 'Alguém';

        switch ($this->type) {
            case 'like':
                return "{$userName} curtiu sua trilha.";

            case 'comment':
                return "{$userName} comentou na sua trilha.";

            default:
                return "Você recebeu uma nova interação.";
        }
    }
}
