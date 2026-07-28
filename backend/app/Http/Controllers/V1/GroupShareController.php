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
    /**
     * list all the groups-share requests
     * @param Request $request
     * @param Group $group
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
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

        //log activity
        activity()
            ->performedOn($share_request->group)
            ->causedBy(auth()->user())
            ->withProperty("requested_by", $share_request->user->name)
            ->log("Share request accepted");

        return response()->noContent();
    }
    public function reject(GroupShare $share_request)
    {
        Gate::authorize("update", $share_request->group);

        $share_request->status = GroupShareStatus::REJECTED;
        $share_request->responded_at = now();
        $share_request->save();

        //log activity
        activity()
            ->performedOn($share_request->group)
            ->causedBy(auth()->user())
            ->withProperty("requested_by", $share_request->user->name)
            ->log("Share request rejected");

        return response()->noContent();

    }
}
