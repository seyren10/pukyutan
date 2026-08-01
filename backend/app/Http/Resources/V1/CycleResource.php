<?php

namespace App\Http\Resources\V1;

use App\Services\LedgerCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CycleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);

        // Merged wholesale (not destructured field-by-field) so this resource
        // automatically picks up new keys the service starts returning —
        // e.g. reserve_balance / recommended_disbursement — without a second
        // edit here every time the service's return shape grows.
        $data = array_merge($data, app(LedgerCalculatorService::class)->collectionSummaryForCycle($this->resource));

        // Per-member status *as of this cycle*, not "as of today" — this is
        // what member rows inside a cycle's detail view should render from.
        // Using group.members here (not a fresh query) relies on the caller
        // having eager-loaded it; falls back to a query if not, at the cost
        // of an extra round trip.
        $ledger = app(LedgerCalculatorService::class);
        $data['members'] = $this->resource->group->members->map(function ($member) use ($ledger) {
            return [
                ...$member->toArray(),
                ...$ledger->balanceForMemberAsOfCycle($member, $this->resource)
            ];
        });



        return $data;
    }
}