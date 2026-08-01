<?php

namespace App\Http\Resources\V1;

use App\Services\LedgerCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        [
            'expected_total' => $expectedTotal,
            'paid_total' => $paidTotal,
            'balance' => $balance
        ] = app(LedgerCalculatorService::class)->balanceForMember($this->resource);

        $data['expected_total'] = $expectedTotal;
        $data['paid_total'] = $paidTotal;
        $data['balance'] = $balance;

        return $data;
    }
}
