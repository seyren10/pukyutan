<?php

namespace App\Http\Controllers\V1;

use App\Enums\GroupActivityEvent;
use App\Enums\GroupShareStatus;
use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\Request;

class GroupLeaveController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Group $group)
    {
        $share = $group->groupShares()
            ->where("user_id", $request->user()->id)
            ->where("status", GroupShareStatus::ACCEPTED)
            ->firstOrFail();

        $share->status = GroupShareStatus::REVOKED;
        $share->responded_at = now();
        $share->save();

        activity("group.{$group->id}")
            ->performedOn($group)
            ->causedBy($request->user())
            ->event(GroupActivityEvent::ShareLeft->value)
            ->withProperty("affected_user", $request->user()->name)
            ->log("{$request->user()->name} left the group");

        return response()->noContent();
    }
}
