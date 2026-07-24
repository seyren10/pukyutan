<?php

namespace App\Http\Controllers\V1;

use App\Enums\GroupShareStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\GroupShareResource;
use App\Models\Group;
use App\Models\GroupShare;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class GroupShareController extends Controller
{
    public function index(Request $request, Group $group)
    {
        Gate::authorize("update", $group);
        $groupShares = $group->groupShares()->get();

        return GroupShareResource::collection($groupShares);
    }

    public function accept(GroupShare $share_request)
    {
        Gate::authorize("update", $share_request->group);

        $share_request->status = GroupShareStatus::ACCEPTED;
        $share_request->responded_at = now();
        $share_request->save();

        return response()->noContent();
    }
    public function reject(GroupShare $share_request)
    {
        Gate::authorize("update", $share_request->group);

        $share_request->status = GroupShareStatus::REJECTED;
        $share_request->responded_at = now();
        $share_request->save();

        return response()->noContent();

    }
}
