<?php

namespace App\Http\Controllers\V1;

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
        $cycle->disbursed_amount = $disbursedAmount;
        $cycle->disbursed_at = now();
        $cycle->save();

        $summary = $ledgerCalculatorService->collectionSummaryForCycle($cycle);

        activity()
            ->performedOn($cycle)
            ->causedBy(auth()->user())
            ->withProperty("disbursed_amount", $disbursedAmount)
            ->log("Cycle Disbursed");

        return response()->json(["cycle" => $cycle, "summary" => $summary]);

    }
}
