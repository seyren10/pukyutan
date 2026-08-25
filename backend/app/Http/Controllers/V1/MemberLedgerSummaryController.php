<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Services\LedgerCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MemberLedgerSummaryController extends Controller
{
    /**
     * Return the totals (expected, paid, balance) for a given member.
     */
    public function __invoke(Request $request, Member $member, LedgerCalculatorService $ledgerCalculatorService)
    {
        Gate::authorize('view', $member);

        $balances = $ledgerCalculatorService->balanceForMember($member);

        return response()->json($balances);
    }
}