<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Cycle;
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

        // `$group->activities()` only covers logs performed on the group
        // itself (activated, round started, completed, share requests). Cycle
        // events — currently just disbursement, see CycleDisburseController —
        // are logged with `performedOn($cycle)`, so they need a second branch
        // scoped to this group's cycle ids rather than a plain morphMany.
        $activities = Activity::query()
            ->where(function ($query) use ($group) {
                $query->where("subject_type", Group::class)
                    ->where("subject_id", $group->id);
            })
            ->orWhere(function ($query) use ($group) {
                $query->where("subject_type", Cycle::class)
                    ->whereIn("subject_id", $group->cycles()->select("id"));
            })
            ->latest()
            ->paginate($perPage);

        return response()->json($activities);
    }
}