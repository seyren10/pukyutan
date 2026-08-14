<?php

namespace App\Http\Controllers\V1;

use App\Enums\GroupShareStatus;
use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupShare;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;

class UserActivityController extends Controller
{
    /**
     * All activity across every group the authenticated user is part of —
     * owned or shared (accepted only). Relies entirely on the "group.{id}"
     * log-name convention established for the per-group feed; no new
     * subject-type branching needed here either.
     */
    public function __invoke(Request $request)
    {
        $user = $request->user();

        $ownedGroupIds = Group::where('user_id', $user->id)->pluck('id');

        $sharedGroupIds = GroupShare::where('user_id', $user->id)
            ->where('status', GroupShareStatus::ACCEPTED)
            ->pluck('group_id');

        $groupIds = $ownedGroupIds->merge($sharedGroupIds)->unique()->values();

        $perPage = min($request->query('per_page', 20), 50);

        if ($groupIds->isEmpty()) {
            return response()->json(
                Activity::query()->whereRaw('1 = 0')->paginate($perPage)
            );
        }

        $logNames = $groupIds->map(fn($id) => "group.{$id}")->all();

        $activities = Activity::inLog($logNames)
            ->latest()
            ->paginate($perPage);

        // Attach which group each row belongs to — the per-group feed can
        // rely on page context for this, but a cross-group feed can't.
        $groupNames = Group::whereIn('id', $groupIds)->pluck('name', 'id');

        $activities->getCollection()->transform(function (Activity $activity) use ($groupNames) {
            $groupId = (int) Str::after($activity->log_name, 'group.');
            $activity->group_id = $groupId;
            $activity->group_name = $groupNames->get($groupId);
            return $activity;
        });

        return response()->json($activities);
    }
}