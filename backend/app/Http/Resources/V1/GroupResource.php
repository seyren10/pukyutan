<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        $data["is_round_completed"] = $this->whenLoaded("cycles", $this->isRoundCompleted());
        $data['invite_code'] = $this->when($request->user()?->id === $this->user_id, $this->invite_code);
        $data['next_cycle'] = new CycleResource($this->whenLoaded('nextCycle'));
        $data['members'] = MemberResource::collection($this->whenLoaded('members'));

        return $data;
    }
}
