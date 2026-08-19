<?php

namespace App\Notifications;

use App\Enums\GroupShareStatus;
use App\Enums\NotificationType;
use App\Models\GroupShare;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GroupShareResponded extends Notification
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
        return NotificationType::ShareResponded->value;
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
        $accepted = $this->share->status === GroupShareStatus::ACCEPTED;

        $mail = (new MailMessage)->subject(
            $accepted
            ? "You've been added to \"{$group->name}\""
            : "Your request to join \"{$group->name}\" was declined"
        );

        return $accepted
            ? $mail->line("Your request to join \"{$group->name}\" has been accepted. You can now view the group.")
            : $mail->line("Your request to join \"{$group->name}\" was declined by the group owner.");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => NotificationType::ShareResponded->value,
            'share_id' => $this->share->id,
            'group_id' => $this->share->group_id,
            'group_name' => $this->share->group->name,
            'status' => $this->share->status,
        ];
    }
}
