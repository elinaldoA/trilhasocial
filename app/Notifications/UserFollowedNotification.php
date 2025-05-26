<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserFollowedNotification extends Notification
{
    use Queueable;

    public $followerName;
    public $followerId;

    public function __construct($followerName, $followerId)
    {
        $this->followerName = $followerName;
        $this->followerId = $followerId;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'follow',
            'user_name' => $this->followerName,
            'follower_id' => $this->followerId,
            'message' => "{$this->followerName} começou a te seguir!",
        ];
    }
}
