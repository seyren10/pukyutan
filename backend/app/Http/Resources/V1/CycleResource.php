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


        $data['members'] = $this->whenLoaded('members', function () {
            $ledger = app(LedgerCalculatorService::class);
            $balances = $ledger->balancesForMembersAsOfCycle($this->resource->members, $this->resource);

            return $this->resource->members->map(fn($member) => [
                ...$member->toArray(),
                ...$balances[$member->id],
            ]);
        });



        return $data;
    }
}