<?php

namespace App\Http\Controllers\V1;

use App\Enums\GroupStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\GroupResource;
use App\Models\Group;
use App\Services\CycleGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class GroupActivateController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Group $group, CycleGeneratorService $cycleGeneratorService)
    {
        // Check if the user is authorized to activate the group
        Gate::authorize("activate", $group);

        if ($group->status !== GroupStatus::DRAFT)
            abort(400, "Group is already activated");

        $cycles = $cycleGeneratorService->generateRound($group, roundNumber: 1);

        $group->cycles()->createMany($cycles);
        $group->status = GroupStatus::ACTIVE;
        $group->save();
        $group->load(["cycles"]);

        //log the activity
        activity()
            ->performedOn($group)
            ->causedBy(auth()->user())
            ->log("Group activated.");

        return new GroupResource($group);
    }
}
