<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\CurrentCycleStats;
use App\DTO\DashboardStats;
use App\DTO\NextPayout;
use App\Enums\GroupStatus;
use App\Models\Cycle;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Collection;

final readonly class DashboardStatsService
{
    public function __construct(private LedgerCalculatorService $ledger) {}

    public function forUser(User $user): DashboardStats
    {
        $groups = Group::query()
            ->where('user_id', $user->id)
            ->withCount('members')
            ->get();

        $openCycles = $this->openCyclesForActiveGroups($groups);

        return new DashboardStats(
            activeGroups: $groups->where('status', GroupStatus::ACTIVE)->count(),
            draftGroups: $groups->where('status', GroupStatus::DRAFT)->count(),
            completedGroups: $groups->where('status', GroupStatus::COMPLETED)->count(),
            membersTotal: (int) $groups->sum('members_count'),
            currentCycle: $this->currentCycleStats($openCycles),
            nextPayout: $this->nextPayout($openCycles),
        );
    }

    private function openCyclesForActiveGroups(Collection $groups): Collection
    {
        $activeGroupIds = $groups->where('status', GroupStatus::ACTIVE)->pluck('id');

        if ($activeGroupIds->isEmpty()) {
            return collect();
        }

        // Earliest undisbursed cycle per group — orderBy first, then unique()
        // keeps the first (soonest) row it sees per group_id.
        return Cycle::query()
            ->whereIn('group_id', $activeGroupIds)
            ->whereNull('disbursed_at')
            ->with(['group', 'recipient'])
            ->orderBy('due_date')
            ->get()
            ->unique('group_id');
    }

    private function currentCycleStats(Collection $openCycles): ?CurrentCycleStats
    {
        if ($openCycles->isEmpty()) {
            return null;
        }

        $collected = 0.0;
        $expected = 0.0;

        foreach ($openCycles as $cycle) {
            $summary = $this->ledger->collectionSummaryForCycle($cycle);
            $collected += $summary['collected_total'];
            $expected += $summary['expected_total'];
        }

        return new CurrentCycleStats($collected, $expected);
    }

    private function nextPayout(Collection $openCycles): ?NextPayout
    {
        $soonest = $openCycles->sortBy('due_date')->first();

        if (!$soonest) {
            return null;
        }

        $summary = $this->ledger->collectionSummaryForCycle($soonest);

        return new NextPayout(
            groupId: $soonest->group_id,
            groupName: $soonest->group->name,
            recipientName: $soonest->recipient->name,
            amount: $summary['expected_total'],
            dueDate: $soonest->due_date,
        );
    }
}