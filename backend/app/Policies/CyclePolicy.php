<?php

namespace App\Policies;

use App\Models\Cycle;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CyclePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the given member belongs to the cycle's group.
     */
    public function addContribution(User $user, Cycle $cycle, int $memberId): bool
    {
        return $cycle->group()
            ->whereHas('members', fn($query) => $query->whereKey($memberId))
            ->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Cycle $cycle): bool
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
    public function update(User $user, Cycle $cycle): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Cycle $cycle): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Cycle $cycle): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Cycle $cycle): bool
    {
        return false;
    }
}
