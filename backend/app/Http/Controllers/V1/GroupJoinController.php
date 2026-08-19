<?php

namespace App\Http\Controllers\V1;

use App\Enums\GroupShareStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\GroupShareResource;
use App\Models\Group;
use App\Notifications\GroupShareRequested;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GroupJoinController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $invite_code)
    {
        $inviteCode = Str::of($invite_code)->upper();
        $group = Group::where("invite_code", $inviteCode)->firstOrFail();

        abort_if($group === null, 404, "No group found with that invitation code");
        abort_if(Auth::id() === $group->user_id, 400, "You cannot invite yourself");

        $share = $request->user()->groupShares()->updateOrCreate(
            [
                "group_id" => $group->id
            ],
            [
                "status" => GroupShareStatus::PENDING,
                "requested_at" => now(),
                "responded_at" => null
            ]
        );

        $group->user->notify(new GroupShareRequested($share));

        // Avoids an extra query — we already have the requesting user in
        // memory, and it's the same user the share belongs to.
        $share->setRelation("user", $request->user());

        return (new GroupShareResource($share))->response();
    }
}