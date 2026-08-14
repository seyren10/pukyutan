<?php

namespace App\Http\Controllers\V1;

use App\Enums\GroupActivityEvent;
use App\Http\Controllers\Controller;
use App\Models\Cycle;
use App\Services\LedgerCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CycleDisburseController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Cycle $cycle, LedgerCalculatorService $ledgerCalculatorService)
    {
        //cycle must belong to a group of auth user making the request
        Gate::authorize("update", $cycle->group);

        abort_if($cycle->disbursed_at !== null, 400, "Can't disbursed twice.");


        $validated = $request->validate(["disbursed_amount" => ['required', 'numeric', 'min:0.01']]);
        $disbursedAmount = (float) $validated["disbursed_amount"];

        $reserveBalance = $ledgerCalculatorService->treasuryBalanceForGroup($cycle->group);

        // Not a soft judgment call — this is a fact about how much money
        // actually exists. The frontend already nudges the leader toward a
        // safe default (this cycle's expected total, capped at reserve) and
        // lets them exceed *that* deliberately, but it can never physically
        // send out more than the group has collected and not yet paid out.
        abort_if(
            $disbursedAmount > $reserveBalance,
            422,
            "Can't disburse more than what's currently on hand (₱" . number_format($reserveBalance, 2) . ")."
        );

        $cycle->disbursed_amount = $disbursedAmount;
        $cycle->disbursed_at = now();
        $cycle->save();

        $summary = $ledgerCalculatorService->collectionSummaryForCycle($cycle->fresh());

        activity("group.{$cycle->group_id}")
            ->performedOn($cycle)
            ->causedBy(auth()->user())
            ->event(GroupActivityEvent::CycleDisbursed->value)
            ->withProperty("disbursed_amount", $disbursedAmount)
            ->log("Cycle {$cycle->cycle_number} disbursed ₱" . number_format($disbursedAmount, 2) . " to {$cycle->recipient->name}.");

        return response()->json(["cycle" => $cycle, "summary" => $summary]);

    }
}