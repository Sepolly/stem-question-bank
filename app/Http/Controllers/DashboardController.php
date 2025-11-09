<?php

namespace App\Http\Controllers;

use App\Enums\QuestionStatus;
use App\Http\Resources\SubjectResource;
use App\Services\EventService;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(
        protected EventService $eventService,
    ) {}

    public function __invoke(int $eventId)
    {

        $currentEvent = $this->eventService->getEventById($eventId);

        return Inertia::render('Dashboard', [
            'showCreateEventModal' => request()->query('showCreateEventModal') ?? session()->get('showCreateEventModal'),
            'questionCount' => $currentEvent->questions()->count(),
            'approvedQuestionCount' => $currentEvent->questions()
                ->where('status', QuestionStatus::APPROVED->value)
                ->count(),
            'pendingQuestionCount' => $currentEvent->questions()
                ->where('status', QuestionStatus::PENDING->value)
                ->count(),
            'rejectedQuestionCount' => $currentEvent->questions()
                ->where('status', QuestionStatus::REJECTED->value)
                ->count(),
            'subjects' => SubjectResource::collection($currentEvent->subjects),
        ]);
    }
}
