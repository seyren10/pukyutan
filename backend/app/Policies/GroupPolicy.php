<?php

namespace App\Policies;

use App\Enums\GroupShareStatus;
use App\Models\Group;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GroupPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Group $group): bool
    {
        if ($user->id === $group->user_id)
            return true;

        return $group->groupShares()
            ->where("user_id", $user->id)
            ->where("status", GroupShareStatus::ACCEPTED)
            ->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Group $group): bool
    {
        return $group->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Group $group): bool
    {
        return $this->update($user, $group);
    }

    public function activate(User $user, Group $group): Response
    {
        if ($group->user_id !== $user->id) {
            return Response::deny();
        }

        return $group->isDraft()
            ? Response::allow()
            : Response::denyWithStatus(400, 'Group is already activated.');
    }

    /**
     * Determine whether the user can start a new round for the group.
     * A group must be active and its current round finished — starting
     * a round on a draft or already-completed group is never valid.
     */
    public function startRound(User $user, Group $group): Response
    {
        if ($group->user_id !== $user->id) {
            return Response::deny();
        }

        return $group->isActive()
            ? Response::allow()
            : Response::denyWithStatus(400, 'The group must be active to start a new round.');
    }

    /**
     * Determine whether the user can mark the group as complete.
     */
    public function complete(User $user, Group $group): Response
    {
        if ($group->user_id !== $user->id) {
            return Response::deny();
        }

        return $group->isActive()
            ? Response::allow()
            : Response::denyWithStatus(400, 'Only an active group can be marked as complete.');
    }

    /**
     * Determine whether the user can disburse a cycle belonging to the group.
     * Guards the same "must be active" rule for the one operation that
     * previously had no group-state check at all.
     */
    public function disburse(User $user, Group $group): Response
    {
        if ($group->user_id !== $user->id) {
            return Response::deny();
        }

        return $group->isActive()
            ? Response::allow()
            : Response::denyWithStatus(400, 'Cycles can only be disbursed while the group is active.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Group $group): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Group $group): bool
    {
        return false;
    }
}