<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreGroupRequest;
use App\Http\Requests\V1\UpdateGroupRequest;
use App\Http\Resources\V1\GroupResource;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $userGroups = $user->groups()->get();

        return GroupResource::collection($userGroups);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGroupRequest $request)
    {
        $validated = $request->validated();

        $group = Auth::user()->groups()->create($validated);

        return (new GroupResource($group))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        Gate::authorize("view", $group);

        $group->load("members", "user:id,name,email");

        return new GroupResource($group);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGroupRequest $request, Group $group)
    {
        Gate::authorize("update", $group);

        $validated = $request->validated();
        $group->update($validated);
        $group->refresh();

        return new GroupResource($group);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        Gate::authorize("delete", $group);

        $group->delete();

        return response()->noContent();
    }
}
