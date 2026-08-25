<?php

namespace App\Http\Controllers\V1;

use App\Enums\GroupActivityEvent;
use App\Enums\GroupShareStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\GroupShareResource;
use App\Models\Group;
use App\Models\GroupShare;
use App\Notifications\GroupShareResponded;
use App\Notifications\GroupShareRevoked;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

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

        // Optional ?status= filter — without it this still returns
        // pending/accepted/rejected/revoked all mixed, same as before.
        $validated = $request->validate([
            "status" => [Rule::enum(GroupShareStatus::class)],
        ]);

        $groupShares = $group->groupShares()
            ->with("user:id,name,email,dicebear_seed,avatar")
            ->when($validated["status"] ?? null, fn($query, $status) => $query->where("status", $status))
            ->latest("requested_at")
            ->get();

        return GroupShareResource::collection($groupShares);
    }

    /**
     * Every PENDING share request across every group the authenticated user
     * owns — the aggregate feed behind the dashboard's "N pending requests"
     * banner, so the owner doesn't have to open each group individually to
     * find out something needs review.
     */
    public function pending(Request $request)
    {
        $perPage = min($request->query("per_page", 15), 50);

        $shares = GroupShare::query()
            ->where("status", GroupShareStatus::PENDING)
            ->whereHas("group", fn($query) => $query->where("user_id", $request->user()->id))
            ->with(["user:id,name,email,dicebear_seed,avatar", "group:id,name"])
            ->latest("requested_at")
            ->paginate($perPage);

        return GroupShareResource::collection($shares);
    }



    public function accept(GroupShare $share_request)
    {
        Gate::authorize("update", $share_request->group);

        $share_request->status = GroupShareStatus::ACCEPTED;
        $share_request->responded_at = now();
        $share_request->save();

        $share_request->user->notify(new GroupShareResponded($share_request));

        //log activity
        activity("group.{$share_request->group->id}")
            ->performedOn($share_request->group)
            ->causedBy(auth()->user())
            ->event(GroupActivityEvent::ShareAccepted->value)
            ->withProperty("requested_by", $share_request->user->name)
            ->log("Share request accepted");

        return new GroupShareResource($share_request);
    }
    public function reject(GroupShare $share_request)
    {
        Gate::authorize("update", $share_request->group);

        $share_request->status = GroupShareStatus::REJECTED;
        $share_request->responded_at = now();
        $share_request->save();

        $share_request->user->notify(new GroupShareResponded($share_request));

        //log activity
        activity("group.{$share_request->group->id}")
            ->performedOn($share_request->group)
            ->causedBy(auth()->user())
            ->event(GroupActivityEvent::ShareRejected->value)
            ->withProperty("requested_by", $share_request->user->name)
            ->log("Share request rejected");

        return new GroupShareResource($share_request);

    }

    /**
     * Owner revokes a previously-accepted share. Self-initiated leaving is
     * a separate endpoint (GroupLeaveController) — that one resolves the
     * caller's own share row internally rather than requiring the frontend
     * to know a share id it was never given.
     */
    public function destroy(GroupShare $share_request)
    {
        Gate::authorize("update", $share_request->group);

        abort_unless(
            $share_request->status === GroupShareStatus::ACCEPTED,
            400,
            "Only an accepted share can be revoked."
        );

        $share_request->status = GroupShareStatus::REVOKED;
        $share_request->responded_at = now();
        $share_request->save();

        activity("group.{$share_request->group->id}")
            ->performedOn($share_request->group)
            ->causedBy(auth()->user())
            ->event(GroupActivityEvent::ShareRevoked->value)
            ->withProperty("affected_user", $share_request->user->name)
            ->log("{$share_request->user->name}'s access was revoked");

        $share_request->user->notify(new GroupShareRevoked($share_request));

        return response()->noContent();
    }
}
