<?php

namespace App\Services;

use App\Models\Group;
use App\Models\Member;
use DB;

class MemberReorderService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function execute(Group $group, array $memberIds)
    {
        // Require the full roster, not a partial list — a partial list
        // would leave the omitted members with stale payout_order values.
        $groupMemberIds = $group->members()->pluck('id')->sort()->values();
        $submittedIds = collect($memberIds)->sort()->values();

        if ($groupMemberIds->all() !== $submittedIds->all()) {
            abort(422, 'member_ids must include every member in the group, exactly once.');
        }

        DB::transaction(function () use ($memberIds) {
            foreach ($memberIds as $index => $memberId) {
                Member::whereKey($memberId)->update(['payout_order' => $index + 1]);
            }
        });
    }
}
