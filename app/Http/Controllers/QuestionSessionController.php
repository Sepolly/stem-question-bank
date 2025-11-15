<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuestionSessionRequest;
use App\Http\Resources\QuestionSessionResource;
use App\Services\QuestionSessionService;
use Inertia\Inertia;

class QuestionSessionController extends Controller
{
    public function __construct(
        private QuestionSessionService $questionSessionService
    ) {}

    public function index(int $eventId)
    {
        $currentSessionId = session()->get('currentSession');

        return Inertia::render('sessions/index', [
            'sessions' => QuestionSessionResource::collection(
                $this->questionSessionService->getAllSessions()
            ),
            'currentSession' => $currentSessionId && QuestionSessionResource::make(
                $this->questionSessionService->getSessionById($currentSessionId)
            ),
            'error' => session()->get('error')
        ]);
    }

    public function createSession(StoreQuestionSessionRequest $request, int $eventId)
    {
        $request->validated();

        ['session' => $session,'error' => $error] = 
            $this->questionSessionService->createSession($request->validated());

        return back()->with([
            'currentSession' => $session ? $session->id : null,
            'error' => $error
        ]);

    }

    public function startSession(int $eventId, int $sessionId)
    {
        $this->questionSessionService->startSession($sessionId);

        return back();
    }

    public function endSession(int $eventId, int $sessionId)
    {
        $this->questionSessionService->endSession($sessionId);
    }
}
