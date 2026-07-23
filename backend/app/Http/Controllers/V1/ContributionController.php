<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreContributionRequest;
use App\Http\Resources\V1\ContributionResource;
use App\Models\Cycle;
use Illuminate\Support\Facades\Gate;

class ContributionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Cycle $cycle)
    {
        $contributions = $cycle->contributions()->simplePaginate();

        return ContributionResource::collection($contributions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContributionRequest $request, Cycle $cycle)
    {
        //member should be part of the group before proceeding
        $memberId = (int) $request->safe()->only("member_id");
        Gate::authorize('addContribution', [$cycle, $memberId]);

        $validated = $request->safe()->except("member_id");
        $contribution = $cycle->contributions()->create($validated);

        return new ContributionResource($contribution)
            ->response()
            ->setStatusCode(201);
    }
}
