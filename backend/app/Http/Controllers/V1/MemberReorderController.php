<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ReorderMemberRequest;
use App\Http\Resources\V1\MemberResource;
use App\Models\Group;
use App\Services\MemberReorderService;
use Illuminate\Support\Facades\Gate;

class MemberReorderController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(ReorderMemberRequest $request, Group $group, MemberReorderService $service)
    {
        Gate::authorize('update', $group);

        $validated = $request->validated();

        $service->execute($group, $validated['member_ids']);

        return MemberResource::collection(
            $group->members()->orderBy('payout_order')->get()
        );
    }
}
