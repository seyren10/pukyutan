<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\Activitylog\Models\Activity;

class GroupActivityController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Group $group)
    {
        Gate::authorize("view", $group);

        $perPage = min($request->query("per_page", 15), 99);

        // Every activity() call for this group — regardless of subject model
        // (Group, Cycle, Contribution, and eventually Member/GroupShare) — is
        // written to the "group.{id}" log via the first argument to
        // activity(). That keeps this query flat: no per-subject-type OR
        // branch to maintain as new auditable actions get added.
        $activities = Activity::inLog("group.{$group->id}")
            ->latest()
            ->paginate($perPage);

        return response()->json($activities);
    }
}