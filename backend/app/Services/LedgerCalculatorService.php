<?php

namespace App\Services;

use App\Models\Contribution;
use App\Models\Cycle;
use App\Models\Group;
use App\Models\Member;
use Illuminate\Support\Collection;

class LedgerCalculatorService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * given a member, tell you what they've paid, what they owe, and their running balance
     *
     * Derived from ledgerForMember() rather than computed independently, so
     * the totals here can never drift out of sync with the per-cycle
     * breakdown — same rows, just summed.
     */
    public function balanceForMember(Member $member): array
    {
        $ledger = $this->ledgerForMember($member);

        $expectedTotal = (float) $ledger->sum('expected');
        $paidTotal = (float) $ledger->sum('paid');

        return [
            'expected_total' => $expectedTotal,
            'paid_total' => $paidTotal,
            'balance' => $expectedTotal - $paidTotal, // positive = owes money, negative = credit
        ];
    }

    /**
     * Given a member, break down their contribution history cycle by cycle —
     * what was expected, what was actually paid (including every individual
     * payment, since a cycle can have several partial contributions), and
     * the running balance as of that cycle.
     *
     * Only cycles that are due-or-past today are included, matching
     * balanceForMember()'s window exactly — a future cycle isn't "owed" yet.
     */
    public function ledgerForMember(Member $member): Collection
    {
        // Same tenure + due-date rules as balanceForMember() used to apply
        // directly: cycles that existed before this member joined (e.g. they
        // replaced someone) don't count, and neither do cycles not yet due.
        $cycles = $member->group->cycles()
            ->has('contributions')
            ->where('created_at', '>=', $member->created_at)
            ->orderBy('cycle_number')
            ->get();

        // One query for every contribution across every relevant cycle,
        // grouped in memory — avoids an N+1 (one query per cycle) that a
        // loop calling something like balanceForMemberAsOfCycle() would
        // otherwise cause here.
        $contributionsByCycle = $member->contributions()
            ->whereIn('cycle_id', $cycles->pluck('id'))
            ->orderBy('paid_at')
            ->get()
            ->groupBy('cycle_id');

        $contributionAmount = (float) $member->group->contribution_amount;
        $runningExpected = 0.0;
        $runningPaid = 0.0;

        return $cycles->map(function (Cycle $cycle) use (&$runningExpected, &$runningPaid, $contributionAmount, $contributionsByCycle) {
            $contributions = $contributionsByCycle->get($cycle->id, collect());
            $paid = (float) $contributions->sum('amount');

            $runningExpected += $contributionAmount;
            $runningPaid += $paid;

            return [
                'cycle_number' => $cycle->cycle_number,
                'round_number' => $cycle->round_number,
                'due_date' => $cycle->due_date->toDateString(),
                'expected' => $contributionAmount,
                'paid' => $paid,
                // positive = owes money, negative = credit — cumulative
                // through this cycle, so an overpayment on an earlier cycle
                // visibly offsets a shortfall on a later one.
                'running_balance' => $runningExpected - $runningPaid,
                'contributions' => $contributions->map(fn(Contribution $contribution) => [
                    'id' => $contribution->id,
                    'amount' => (float) $contribution->amount,
                    'paid_at' => $contribution->paid_at?->toDateString(),
                    'notes' => $contribution->notes,
                ])->values(),
            ];
        });
    }

    public function collectionSummaryForCycle(Cycle $cycle): array
    {
        $expectedPerMember = $cycle->group->contribution_amount;
        $activeMemberCount = $cycle->group->members()->count();


        $expectedTotal = $expectedPerMember * $activeMemberCount;
        $collectedTotal = (float) $cycle->contributions()->sum('amount');

        // Everything currently sitting with the group, undisbursed. This
        // already includes $collectedTotal above — contributions belong to
        // this cycle, and the reserve sums across every cycle ever — so it's
        // the true pot available to hand out right now, including any
        // surplus rolled forward from a previous cycle's overpayment.
        $reserveBalance = $this->treasuryBalanceForGroup($cycle->group);

        // Never recommend handing out more than this cycle actually needs
        // (any extra stays banked, backing next cycle's members who've
        // already overpaid) or more than physically exists in reserve
        // (can't disburse money the group doesn't have).
        $recommendedDisbursement = max(min($expectedTotal, $reserveBalance), 0);

        return [
            'expected_total' => $expectedTotal,
            'collected_total' => $collectedTotal,
            'reserve_balance' => $reserveBalance,
            'recommended_disbursement' => $recommendedDisbursement,
        ];
    }

    /**
     * How much money the group actually has on hand right now, undisbursed —
     * every contribution ever paid in, minus every amount ever sent out.
     *
     * This is what makes an overpayment in one cycle correctly fund a
     * shortfall in a later one: nothing "moves" between cycles, the reserve
     * is just a running total that naturally carries surplus forward.
     */
    public function treasuryBalanceForGroup(Group $group): float
    {
        $totalCollected = (float) Contribution::query()
            ->whereHas('cycle', fn($query) => $query->where('group_id', $group->id))
            ->sum('amount');

        $totalDisbursed = (float) $group->cycles()
            ->whereNotNull('disbursed_at')
            ->sum('disbursed_amount');

        return $totalCollected - $totalDisbursed;
    }

    /**
     * Same idea as balanceForMember(), but scoped to "as of this specific
     * cycle" instead of "as of today". These genuinely differ whenever a
     * cycle's due_date is still in the future — which is the normal case
     * the moment a leader opens the app to start collecting for it. Using
     * cycle_number (not due_date <= now()) means this is correct regardless
     * of what day it happens to be when the leader looks.
     */
    public function balanceForMemberAsOfCycle(Member $member, Cycle $cycle): array
    {
        $cyclesElapsed = $member->group->cycles()
            ->where('created_at', '>=', $member->created_at)
            ->where('cycle_number', '<=', $cycle->cycle_number)
            ->count();

        $expectedTotal = $cyclesElapsed * $member->group->contribution_amount;

        $paidTotal = (float) $member->contributions()
            ->whereHas('cycle', fn($query) => $query->where('cycle_number', '<=', $cycle->cycle_number))
            ->sum('amount');

        return [
            'expected_total' => $expectedTotal,
            'paid_total' => $paidTotal,
            'balance' => $expectedTotal - $paidTotal,
        ];
    }

    public function balancesForMembersAsOfCycle(Collection $members, Cycle $cycle): array
    {
        $memberIds = $members->pluck('id');

        $paidTotals = Contribution::query()
            ->whereIn('member_id', $memberIds)
            ->whereHas('cycle', fn($q) => $q->where('due_date', '<=', $cycle->due_date))
            ->groupBy('member_id')
            ->selectRaw('member_id, SUM(amount) as total_paid')
            ->pluck('total_paid', 'member_id');

        $cyclesElapsed = Cycle::query()
            ->where('group_id', $cycle->group_id)
            ->where('due_date', '<=', $cycle->due_date)
            ->count();

        $expectedPerMember = $cyclesElapsed * $cycle->group->contribution_amount;

        return $members->mapWithKeys(function ($member) use ($paidTotals, $expectedPerMember) {
            $paid = (float) ($paidTotals[$member->id] ?? 0);

            return [
                $member->id => [
                    'expected_total' => $expectedPerMember,
                    'paid_total' => $paid,
                    // Positive = owes money. Negative = has credit.
                    // Rollover is free — this single subtraction already
                    // accounts for every over/underpayment across every
                    // prior cycle. No per-cycle bookkeeping needed.
                    'balance' => $expectedPerMember - $paid,
                ],
            ];
        })->all();
    }
}