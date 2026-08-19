<?php

namespace App\Notifications;

use App\Enums\NotificationType;
use App\Models\GroupShare;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GroupShareRequested extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected GroupShare $share)
    {
        //
    }

    /**
     * Get the notification's database type.
     */
    public function databaseType(object $notifiable): string
    {
        return NotificationType::ShareRequested->value;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $group = $this->share->group;
        $requester = $this->share->user;

        return (new MailMessage)
            ->subject("New request to join \"{$group->name}\"")
            ->line("{$requester->name} has requested to join \"{$group->name}\".")
            ->line('You can accept or reject this request at any time from the group\'s share requests.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => NotificationType::ShareRequested->value,
            'share_id' => $this->share->id,
            'group_id' => $this->share->group_id,
            'group_name' => $this->share->group->name,
            'requester_id' => $this->share->user_id,
            'requester_name' => $this->share->user->name,
        ];
    }
}
