<?php

namespace App\Policies;

use App\Enums\QuestionStatus;
use App\Models\Question;
use App\Models\User;
use App\Services\EventService;

class QuestionPolicy
{
    public function __construct(
        protected EventService $eventService,
    ) {}

    public function before(User $user, $ability)
    {
        if ($user->hasAnyRole(['admin', 'super admin'])) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return isset($user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Question $question): bool
    {
        return $user->inEvent($question->event->id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        $eventId = $this->eventService->getCurrentEvent()->id;

        return $user->inEvent($eventId)
        && $user->hasAnyRole(['admin', 'super admin', 'contributor']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Question $question): bool
    {
        // user must belong to the event
        if (! $user->inEvent($question->event_id)) {
            return false;
        }

        // Author can update only if the question is still pending
        return $question->author()->is($user)
            && $question->status === QuestionStatus::PENDING->value;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Question $question): bool
    {
        if (! $user->inEvent($question->event_id)) {
            return false;
        }

        return $question->author()->is($user)
            && $question->status !== QuestionStatus::APPROVED->value;

    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Question $question): bool
    {
        return $user->inEvent($question->event->id) && $user->hasAnyRole(['admin', 'super admin']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Question $question): bool
    {
        return $user->inEvent($question->event->id) && $user->hasAnyRole(['admin', 'super admin']);
    }
}
