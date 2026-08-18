<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ActivityResource;
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

        $perPage = min($request->query("per_page", 30), 99);

        $activities = Activity::inLog("group.{$group->id}")
            ->latest()
            ->simplePaginate($perPage);

        return ActivityResource::collection(($activities));
    }
}