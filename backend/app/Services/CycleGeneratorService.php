<?php

namespace App\Services;

use App\Enums\FrequencyUnitType;
use App\Models\Cycle;
use App\Models\Group;
use Carbon\Carbon;

class CycleGeneratorService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Generate the cycle data for a new round of a group.
     *
     * Does NOT persist anything — returns an array of arrays ready for
     * insertion (e.g. via Cycle::insert() or a bulk create). Keeping this
     * pure (no DB writes inside) is what makes it easy to unit test.
     */
    public function generateRound(Group $group, int $roundNumber): array
    {
        // 1. Get active members ordered by payout_order
        $members = $group->members()
            ->orderBy("payout_order")
            ->get();


        // 2. Figure out the starting cycle_number and starting due_date
        //    for this round (different logic for round 1 vs later rounds —
        //    see notes below)
        [$startingCycleNumber, $startingDueDate] = $this->resolveRoundStart($group);

        // 3. Loop through members, build one array per cycle
        $cycles = [];

        foreach ($members as $index => $member) {
            $cycles[] = [
                'group_id' => $group->id,
                'round_number' => $roundNumber,
                'cycle_number' => $startingCycleNumber + $index,
                'due_date' => $this->advanceDate(
                    $startingDueDate,
                    $group->frequency_unit,
                    $group->frequency_interval,
                    $index // how many "steps" forward from the round's start
                ),
                'recipient_member_id' => $member->id,
                'disbursed_at' => null,
                'disbursed_amount' => null,
            ];
        }

        // 4. Return the array
        return $cycles;
    }

    /**
     * Move a date forward by a number of "steps", where each step is
     * one frequency_interval of the given unit.
     *
     * This is the one method worth testing carefully on its own — the
     * 'month' branch is the only one with a real gotcha (see below).
     */
    protected function advanceDate(Carbon $baseDate, FrequencyUnitType $unit, int $interval, int $steps): Carbon
    {
        // ->copy() matters here! Carbon dates are mutable — without copy(),
        // calling ->addDays() etc. would modify $baseDate itself, which
        // would corrupt every subsequent loop iteration that reuses it.
        return match ($unit) {
            FrequencyUnitType::DAILY => $baseDate->copy()->addDays($interval * $steps),
            FrequencyUnitType::WEEKLY => $baseDate->copy()->addWeeks($interval * $steps),
                // addMonthsNoOverflow (not addMonths!) is deliberate.
                // Example: Jan 31 + 1 month.
                //   addMonths()          -> Mar 3  (rolls over, since Feb has no 31st)
                //   addMonthsNoOverflow() -> Feb 28 (clamps to the last valid day)
                // The clamped behavior is what you want for a predictable
                // recurring schedule.
            FrequencyUnitType::MONTHLY => $baseDate->copy()->addMonthsNoOverflow($interval * $steps),
        };
    }

    /**
     * Figure out the starting cycle_number and starting due_date for a round.
     *
     * Round 1 always starts fresh from the group's start_date.
     * Round 2+ continues on from wherever the previous round left off —
     * both in numbering (cycle_number keeps climbing, never resets) and
     * in timing (the next cycle picks up one interval after the last one).
     */
    protected function resolveRoundStart(Group $group): array
    {
        $existingCycleCount = $group->cycles()->count();
        $lastDueDate = $group->cycles()->max('due_date');

        if ($existingCycleCount === 0) {
            // No cycles exist yet — this IS round 1.
            return [1, $group->start_date];
        }

        // A later round: keep counting cycle numbers upward, and start
        // the clock one interval after the last cycle that already exists.
        $nextCycleNumber = $existingCycleCount + 1;
        $nextDueDate = $this->advanceDate(
            Carbon::parse($lastDueDate),
            $group->frequency_unit,
            $group->frequency_interval,
            1 // exactly one interval past the last cycle
        );

        return [$nextCycleNumber, $nextDueDate];
    }
}
