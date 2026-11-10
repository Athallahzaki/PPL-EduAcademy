<?php

namespace App\Policies;

use App\Enums\AccountStatus;
use App\Enums\Role;
use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $actor): bool
    {
        return $actor->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $actor, User $target): bool
    {
        return $actor->isAdmin();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $actor): bool
    {
        return $actor->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $actor, User $target): bool
    {
        return $actor->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $actor, User $target): bool
    {
        if (! $actor->isAdmin()) {
            return false;
        }

        // Admin MUST NOT delete their own account
        if ($actor->id === $target->id) {
            return false;
        }

        // The last active admin MUST NOT be deleted
        if ($target->isAdmin() && $target->isActive() && User::activeAdminCount() <= 1) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $actor, User $target): bool
    {
        return $actor->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $actor, User $target): bool
    {
        return false;
    }

    /**
     * Determine whether the actor can change the target's role.
     */
    public function changeRole(User $actor, User $target, Role $newRole): bool
    {
        if (! $actor->isAdmin()) {
            return false;
        }

        // Admin MUST NOT change the role of their own account
        if ($actor->id === $target->id) {
            return false;
        }

        // The last active admin MUST NOT be demoted
        if ($target->isAdmin() && $target->isActive() && $newRole !== Role::Admin && User::activeAdminCount() <= 1) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the actor can change the target's status.
     */
    public function changeStatus(User $actor, User $target, AccountStatus $newStatus): bool
    {
        if (! $actor->isAdmin()) {
            return false;
        }

        // Admin MUST NOT suspend their own account
        if ($newStatus === AccountStatus::Suspended && $actor->id === $target->id) {
            return false;
        }

        // The last active admin MUST NOT be suspended
        if ($target->isAdmin() && $target->isActive() && $newStatus === AccountStatus::Suspended && User::activeAdminCount() <= 1) {
            return false;
        }

        // Status change MUST follow the transition table
        return $target->status->canTransitionTo($newStatus);
    }

    /**
     * Determine whether the actor can reset the target's password.
     */
    public function resetPassword(User $actor, User $target): bool
    {
        if (! $actor->isAdmin()) {
            return false;
        }

        // Admin cannot reset their own password via admin panel (use change password page)
        return $actor->id !== $target->id;
    }
}

