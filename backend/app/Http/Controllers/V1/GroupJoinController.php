<?php

namespace App\Http\Controllers\V1;

use App\Enums\GroupShareStatus;
use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupShare;
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

        GroupShare::updateOrCreate(
            [
                "group_id" => $group->id
            ],
            [
                "status" => GroupShareStatus::PENDING,
                "requested_at" => now(),
                "responded_at" => null
            ]
        );

        return response()->json(["message" => "Request has been made. Wait for the owner to confirm"]);
    }
}
