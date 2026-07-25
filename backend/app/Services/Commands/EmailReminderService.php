<?php

namespace App\Services\Commands;

use App\Models\Cycle;
use App\Models\CycleReminder;
use App\Notifications\CycleDueReminder;
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
            ->with('group.members')
            ->get();
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
