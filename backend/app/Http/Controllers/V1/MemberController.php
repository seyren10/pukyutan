<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreMemberRequest;
use App\Http\Requests\V1\UpdateMemberRequest;
use App\Http\Resources\V1\MemberResource;
use App\Models\Group;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Group $group)
    {
        Gate::authorize("view", $group);
        $members = $group->members()->get();

        return MemberResource::collection($members);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMemberRequest $request, Group $group)
    {

        if (!$group->isDraft())
            abort(400, "Cannot add members to an active group");


        $validated = $request->validated();
        $validated["payout_order"] = $group->members()->max("payout_order") + 1;
        $member = $group->members()->create($validated);

        return (new MemberResource($member))
            ->response()
            ->setStatusCode(201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Member $member)
    {
        Gate::authorize("view", $member);

        return new MemberResource($member);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMemberRequest $request, Member $member)
    {
        Gate::authorize("update", $member);

        $validated = $request->validated();
        $member->update($validated);
        $member->refresh();

        return new MemberResource($member);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
        Gate::authorize("delete", $member);

        if (!$member->group->isDraft())
            abort(400, "Cannot delete members from an active group");

        $member->delete();

        return response()->noContent();
    }
}
