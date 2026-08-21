<?php

namespace App\Policies;

use App\Models\Contribution;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ContributionPolicy
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
    public function view(User $user, Contribution $contribution): bool
    {
        return false;
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
    public function update(User $user, Contribution $contribution): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Contribution $contribution): Response
    {
        $group = $contribution->group;

        if (!$group) {
            return Response::denyAsNotFound();
        }

        $currentCycle = $group->nextCycle()->first();

        return $group->user_id === $user->id
            && $currentCycle !== null
            && $contribution->cycle_id === $currentCycle->id
            ? Response::allow()
            : Response::denyWithStatus(400, 'You cannot delete a contribution from past cycles.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Contribution $contribution): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Contribution $contribution): bool
    {
        return false;
    }
}
