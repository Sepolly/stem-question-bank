<?php

namespace App\Policies;

use App\Models\Subject;
use App\Models\User;

class SubjectPolicy
{
    public function before(User $user, $abilities)
    {
        if ($user->hasRole('super admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, Subject $subject): bool
    {
        return $user->inEvent($subject->event->id);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Subject $subject): bool
    {
        return $user->inEvent($subject->event->id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Subject $subject): bool
    {
        return $user->hasRole('admin') && $user->inEvent($subject->event->id);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Subject $subject): bool
    {
        return $user->hasRole('admin') && $user->inEvent($subject->event->id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Subject $subject): bool
    {
        return $user->hasRole('admin') && $user->inEvent($subject->event->id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Subject $subject): bool
    {
        return $user->hasRole('admin') && $user->inEvent($subject->event->id);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Subject $subject): bool
    {
        return $user->hasRole('admin') && $user->inEvent($subject->event->id);
    }
}
