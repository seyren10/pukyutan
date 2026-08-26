<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Services\LedgerCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MemberLedgerController extends Controller
{
    /**
     * Return the given member's contribution history, cycle by cycle.
     */
    public function __invoke(Request $request, Member $member, LedgerCalculatorService $ledgerCalculatorService)
    {
        Gate::authorize('view', $member->group);

        $ledger = $ledgerCalculatorService->ledgerForMember($member);

        return response()->json($ledger);
    }
}