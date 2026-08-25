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

        // Opt-in via ?include=summary — computing this touches the ledger
        // service for every member, so it only runs where a caller actually
        // asked for it (e.g. the dedicated members page), not on every place
        // this resource is used (like the group-detail sidebar's roster).
        $data['summary'] = $this->when(
            $this->requestedInclude($request, 'summary'),
            fn() => app(LedgerCalculatorService::class)->balanceForMember($this->resource),
        );

        return $data;
    }

    /**
     * Whether the given key was requested via the comma-separated
     * ?include= query param (e.g. "include=summary" or "include=summary,foo").
     */
    private function requestedInclude(Request $request, string $key): bool
    {
        $includes = array_filter(array_map('trim', explode(',', (string) $request->query('include', ''))));

        return in_array($key, $includes, true);
    }
}
