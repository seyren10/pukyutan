<?php

namespace App\Services\Commands;

use App\Enums\NotificationType;
use App\Models\Cycle;
use App\Models\CycleReminder;
use App\Notifications\CycleDueReminder;
use App\Notifications\CycleDueReminderOwner;
use Notification;

class EmailReminderService
{
    /**
     * Send reminders for all cycles due within the given number of days.
     * Returns how many reminders were actually sent (useful for the
     * command's output, and for asserting on in tests).
     */
    public function sendDueReminders(int $daysAhead): int
    {
        $cycles = $this->cyclesDueWithin($daysAhead);

        $sentCount = 0;

        foreach ($cycles as $cycle) {
            foreach ($cycle->group->members as $member) {
                if ($this->remind($cycle, $member)) {
                    $sentCount++;
                }
            }
            // Notify the group owner via database notification (once per cycle)
            if ($this->notifyOwner($cycle)) {
                $sentCount++;
            }
        }

        return $sentCount;
    }

    /**
     * Fetch cycles due within the window, not yet disbursed.
     * Broken out on its own since it's a reusable, independently
     * testable piece of logic.
     */
    public function cyclesDueWithin(int $daysAhead)
    {
        return Cycle::query()
            ->whereBetween('due_date', [now()->startOfDay(), now()->addDays($daysAhead)->endOfDay()])
            ->whereNull('disbursed_at')
            ->with('group.members', 'group.user')
            ->get();
    }

    /**
     * Notify the group owner via the database channel about an upcoming cycle.
     * Returns true if a notification was created, false if skipped (no owner or already notified).
     */
    public function notifyOwner(Cycle $cycle): bool
    {
        $owner = $cycle->group->user ?? null;

        if (!$owner) {
            return false;
        }

        $exists = $owner->notifications()
            ->where('type', NotificationType::CycleReminder)
            ->where('data->cycle_id', $cycle->id)
            ->exists();

        if ($exists) {
            return false;
        }

        $owner->notify(new CycleDueReminderOwner($cycle));

        return true;
    }

    /**
     * Attempt to remind a single member about a single cycle.
     * Returns true if a reminder was actually sent, false if skipped
     * (no email on file, or already reminded before).
     */
    public function remind(Cycle $cycle, $member): bool
    {
        if (!$member->email) {
            return false;
        }

        $reminder = CycleReminder::firstOrCreate(
            ['cycle_id' => $cycle->id, 'member_id' => $member->id],
            ['sent_at' => now()]
        );

        if (!$reminder->wasRecentlyCreated) {
            return false;
        }

        Notification::route('mail', $member->email)
            ->notify(new CycleDueReminder($cycle));
        return true;
    }
}
