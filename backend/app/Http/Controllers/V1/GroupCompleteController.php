<?php

namespace App\Http\Controllers\V1;

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
        Gate::authorize("update", $group);
        abort_unless($group->isRoundCompleted(), 400, "Current round is not yet completed.");

        $group->status = GroupStatus::COMPLETED;
        $group->save();

        //log the activity
        activity()
            ->performedOn($group)
            ->causedBy(auth()->user())
            ->log("Group marked as completed");
            
        return response()->noContent();
    }
}
