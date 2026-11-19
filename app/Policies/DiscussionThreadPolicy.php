<?php

namespace App\Policies;

use App\Models\DiscussionThread;
use App\Models\User;

class DiscussionThreadPolicy
{
    /**
     * Determine whether the user can view any threads.
     */
    public function viewAny(User $user): bool
    {
        return $user->isActive();
    }

    /**
     * Determine whether the user can view the thread.
     */
    public function view(User $user, DiscussionThread $thread): bool
    {
        return $user->isActive();
    }

    /**
     * Determine whether the user can create threads.
     */
    public function create(User $user): bool
    {
        return $user->isActive();
    }

    /**
     * Determine whether the user can update the thread.
     */
    public function update(User $user, DiscussionThread $thread): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isActive() && $user->id === $thread->user_id;
    }

    /**
     * Determine whether the user can delete the thread.
     */
    public function delete(User $user, DiscussionThread $thread): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isActive() && $user->id === $thread->user_id;
    }
}
