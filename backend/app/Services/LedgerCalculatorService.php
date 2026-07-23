<?php

namespace App\Services;

use App\Models\Cycle;
use App\Models\Member;

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
     */
    public function balanceForMember(Member $member): array
    {
        // 1. Get all cycles that existed during this member's active tenure —
        //    i.e. cycles created on/after this member joined. This matters for
        //    replaced members: a new member shouldn't be charged for cycles
        //    that happened before they existed.
        $cyclesElapsed = $member->group->cycles()
            ->where('created_at', '>=', $member->created_at)
            ->where("due_date", "<=", now())
            ->count();

        $expectedTotal = $cyclesElapsed * $member->group->contribution_amount;

        // 2. Sum everything this member has actually paid, across all their
        //    contribution rows (this is where partial payments and
        //    overpayments naturally net out — no special-case code needed).
        $paidTotal = (float) $member->contributions()->sum('amount');
        $balance = $expectedTotal - $paidTotal;

        return [
            'expected_total' => $expectedTotal,
            'paid_total' => $paidTotal,
            'balance' => $balance, // positive = owes money, negative = credit
        ];
    }

    public function collectionSummaryForCycle(Cycle $cycle): array
    {
        $expectedPerMember = $cycle->group->contribution_amount;
        $activeMemberCount = $cycle->group->members()->count();


        $expectedTotal = $expectedPerMember * $activeMemberCount;
        $collectedTotal = (float) $cycle->contributions()->sum('amount');
        
        return [
            'expected_total' => $expectedTotal,
            'collected_total' => $collectedTotal,
        ];
    }
}
