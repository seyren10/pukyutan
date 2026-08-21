<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardStatsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'groups' => $this->groups,
            'members_total' => $this->members_total,
            'current_cycle' => $this->current_cycle,
            'next_payout' => $this->next_payout,
        ];
    }
}
