<?php

namespace App\Notifications;

use App\Enums\NotificationType;
use App\Models\Cycle;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CycleDueReminderOwner extends Notification
{
    use Queueable;

    public function __construct(protected Cycle $cycle)
    {
        //
    }

    /**
     * Get the notification's database type.
     */
    public function databaseType(object $notifiable): string
    {
        return NotificationType::CycleReminder->value;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int,string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification for the database channel.
     *
     * @return array<string,mixed>
     */
    public function toArray(object $notifiable): array
    {
        $group = $this->cycle->group;

        return [
            'type' => NotificationType::CycleReminder->value,
            'cycle_id' => $this->cycle->id,
            'cycle_number' => $this->cycle->cycle_number,
            'due_date' => $this->cycle->due_date?->toDateString(),
            'group_id' => $group->id,
            'group_name' => $group->name,
            'contribution_amount' => $group->contribution_amount,
            'message' => "Cycle #{$this->cycle->cycle_number} for '{$group->name}' is due on {$this->cycle->due_date?->format('F j, Y')}.",
        ];
    }
}
