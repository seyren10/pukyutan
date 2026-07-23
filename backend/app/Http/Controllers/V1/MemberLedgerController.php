<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Services\LedgerCalculatorService;
use Illuminate\Http\Request;

class MemberLedgerController extends Controller
{
    /**
     * Return the balance for a given member
     */
    public function __invoke(Request $request, Member $member, LedgerCalculatorService $ledgerCalculatorService)
    {
        $balances = $ledgerCalculatorService->balanceForMember($member);

        return response()->json($balances);
    }
}
