<?php

namespace App\Http\Controllers\V1;

use App\Enums\GroupActivityEvent;
use App\Enums\GroupStatus;
use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class GroupCompleteController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Group $group)
    {
        // Ownership + "must currently be active" both live in the policy —
        // stops this from being callable a second time on a group that's
        // already completed.
        Gate::authorize("complete", $group);
        abort_unless($group->isRoundCompleted(), 400, "Current round is not yet completed.");

        $group->status = GroupStatus::COMPLETED;
        $group->save();

        //log the activity
        activity("group.{$group->id}")
            ->performedOn($group)
            ->causedBy(auth()->user())
            ->event(GroupActivityEvent::GroupCompleted->value)
            ->log("Group marked as completed");
            
        return response()->noContent();
    }
}