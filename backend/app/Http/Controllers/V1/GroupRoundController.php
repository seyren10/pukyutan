<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\GroupResource;
use App\Models\Group;
use App\Services\CycleGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class GroupRoundController extends Controller
{
    /**
     * Start a new Round
     */
    public function __invoke(Request $request, Group $group, CycleGeneratorService $service)
    {
        Gate::authorize("update", $group);

        abort_unless($group->isRoundCompleted(), 400, "Current round is not yet completed.");

        $currentRoundNumber = $group->cycles()->max("round_number") ?? 0;
        $nextRoundNumber = $currentRoundNumber + 1;

        $cycles = $service->generateRound($group, $nextRoundNumber);
        $group->cycles()->createMany($cycles);
        $group->load(["cycles"]);

        activity()
            ->performedOn($group)
            ->causedBy(auth()->user())
            ->log("Round {$nextRoundNumber} started.");

        return new GroupResource($group);
    }
}
