<?php

namespace App\Policies;

use App\Models\Scholarship;
use App\Models\User;

class ScholarshipPolicy
{
    /**
     * Determine whether the user can view any scholarships.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the scholarship.
     */
    public function view(User $user, Scholarship $scholarship): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can create scholarships.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the scholarship.
     */
    public function update(User $user, Scholarship $scholarship): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the scholarship.
     */
    public function delete(User $user, Scholarship $scholarship): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the scholarship.
     */
    public function restore(User $user, Scholarship $scholarship): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the scholarship.
     */
    public function forceDelete(User $user, Scholarship $scholarship): bool
    {
        return $user->isAdmin();
    }
}
