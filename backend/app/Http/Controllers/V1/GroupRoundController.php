<?php

namespace App\Http\Controllers\V1;

use App\Enums\GroupActivityEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\GroupResource;
use App\Models\Contribution;
use App\Models\Group;
use App\Services\CycleGeneratorService;
use App\Services\LedgerCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class GroupRoundController extends Controller
{
    /**
     * Start a new Round
     */
    public function newRound(Request $request, Group $group, CycleGeneratorService $service)
    {
        Gate::authorize("update", $group);

        abort_unless($group->isRoundCompleted(), 400, "Current round is not yet completed.");

        $currentRoundNumber = $group->cycles()->max("round_number") ?? 0;
        $nextRoundNumber = $currentRoundNumber + 1;

        $cycles = $service->generateRound($group, $nextRoundNumber);
        $group->cycles()->createMany($cycles);
        $group->load(["cycles"]);

        activity("group.{$group->id}")
            ->performedOn($group)
            ->causedBy(auth()->user())
            ->event(GroupActivityEvent::RoundStarted->value)
            ->log("Round {$nextRoundNumber} started.");

        return new GroupResource($group);
    }

    public function summary(Group $group, int $roundNumber, LedgerCalculatorService $ledger)
    {
        Gate::authorize('view', $group);

        $cycles = $group->cycles()->where('round_number', $roundNumber)->get();

        abort_if($cycles->isEmpty(), 404, "Round {$roundNumber} not found.");

        $totalExpected = $cycles->count() * $group->contribution_amount;
        $totalCollected = Contribution::query()
            ->whereIn('cycle_id', $cycles->pluck('id'))
            ->sum('amount');

        // Balances as of the round's last cycle reflect everyone's standing
        // once the whole round has played out.
        $lastCycle = $cycles->sortByDesc('due_date')->first();
        $balances = $ledger->balancesForMembersAsOfCycle($group->members, $lastCycle);
        $membersWithOutstandingBalance = collect($balances)
            ->filter(fn($balance) => $balance['balance'] > 0)
            ->count();

        return response()->json([
            'round_number' => $roundNumber,
            'is_complete' => $cycles->every(fn($cycle) => $cycle->disbursed_at !== null),
            'total_collected' => (float) $totalCollected,
            'total_expected' => (float) $totalExpected,
            'members_with_outstanding_balance' => $membersWithOutstandingBalance,
        ]);
    }
}
