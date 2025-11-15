<?php

namespace App\Observers;

use App\Models\Activity;
use App\Models\Question;
use App\Services\EventService;

class QuestionObserver
{
    public function __construct(
        protected EventService $eventService
    ){}
    /**
     * Handle the Question "created" event.
     */
    public function created(Question $question): void
    {
        $this->eventService->getCurrentEvent()
            ->activities()
            ->create([
                'title' => 'New question added',
                'type' => 'new_question',
                'description' => $question->title
            ]);
    }

    /**
     * Handle the Question "updated" event.
     */
    public function updated(Question $question): void
    {
        //
    }

    /**
     * Handle the Question "deleted" event.
     */
    public function deleted(Question $question): void
    {
        //
    }

    /**
     * Handle the Question "restored" event.
     */
    public function restored(Question $question): void
    {
        //
    }

    /**
     * Handle the Question "force deleted" event.
     */
    public function forceDeleted(Question $question): void
    {
        //
    }
}
