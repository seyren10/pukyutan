<?php

namespace App\Http\Controllers\V1;

use App\Enums\GroupShareStatus;
use App\Enums\GroupStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreGroupRequest;
use App\Http\Requests\V1\UpdateGroupRequest;
use App\Http\Resources\V1\GroupResource;
use App\Models\Group;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $perPage = min($request->query("per_page", 15), 99);
        $search = $request->query('search', null);
        $status = GroupStatus::tryFrom($request->query('status'));
        $sortBy = $request->query('sort_by');
        $sortDir = $request->query('sort_dir');
        if (!in_array($sortDir, ['asc', 'desc']))
            $sortDir = 'desc';

        $userGroups = $user->groups()
            ->when($search, fn(Builder $query) => $query->whereLike('name', "%{$search}%"))
            ->when($status, fn(Builder $query) => $query->where('status', $status))
            ->when($sortBy, fn(Builder $query) => $query->orderBy($sortBy, $sortDir))
            ->with(["recentMembers", 'nextCycle', 'nextCycle.recipient:id,name', 'cycles'])
            ->withCount(['members', 'cycles'])
            ->latest()
            ->paginate($perPage);

        return GroupResource::collection($userGroups);
    }

    /**
     * List all the groups that are shared to a user
     */
    public function shared(Request $request)
    {
        $user = Auth::user();
        $perPage = min($request->query("per_page", 15), 99);

        $groups = Group::query()->
            whereHas("groupShares", fn($query) =>
                $query->where("user_id", $user->id)
                    ->where("status", GroupShareStatus::ACCEPTED))
            ->with(["user:id,name", 'recentMembers', 'nextCycle'])
            ->withCount(["members"])
            ->latest()
            ->paginate($perPage);

        return GroupResource::collection($groups);
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

        $group->load([
            "user:id,name,email",
            'nextCycle.recipient:id,name',
            'nextCycle.members',
            'members',
            // withSum adds a `contributions_sum_amount` attribute to each cycle
            // via one aggregate query — lets the group-detail timeline show how
            // much came in per cycle without an N+1 or a per-cycle resource call.
            'cycles' => fn($query) => $query->withSum('contributions', 'amount'),
        ]);
        $group->loadCount(['cycles']); // kept — cycles_count is a separate, still-useful field

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