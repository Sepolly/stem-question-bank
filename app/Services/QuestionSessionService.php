<?php

namespace App\Services;

use App\Enums\QuestionSessionStatus;
use App\Models\Question;
use App\Models\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class QuestionSessionService
{
    public function __construct(
        protected EventService $eventService
    ) {}

    public function getSessionById(?int $sessionId)
    {
        try {
            return $sessionId ? Session::query()
                ->with('questions.answer')
                ->findOrFail($sessionId) : null;
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return null;
        }
    }

    public function getAllSessions()
    {
        try {
            return $this->eventService
                ->getCurrentEvent()
                ->sessions()
                ->with(['questions.options','questions.answer'])
                ->get();
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
        }
    }

    public function createSession(array $data): array
    {
        try {
            if($this->eventService->getCurrentEvent()->questions()->count() == 0){
                return [
                    'error' => 'No questions found for this event',
                    'session' => null
                ];
            }
            $session = DB::transaction(function () use ($data) {
                $session = $this->eventService
                    ->getCurrentEvent()
                    ->sessions()
                    ->create([
                        'title' => $data['title'],
                        'difficulty' => $data['difficulty'],
                        'type' => $data['type'],
                        'round' => $data['round'],
                        'number_of_questions' => $data['number_of_questions'],
                        'starts_at' => $data['starts_at'] ?? null,
                        'ends_at' => $data['ends_at'] ?? null,
                    ]);

                $questions = Question::query()
                    ->take($data['number_of_questions'])
                    ->where('difficulty', $data['difficulty'])
                    ->where('type', $data['type'])
                    ->where('round', $data['round'])
                    ->inRandomOrder();

                $questions->update([
                    'session_id' => $session->id,
                ]);

                return $session;
            });

            return [
                'error' => null,
                'session' => $session
            ];
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            return [
                'error' => 'error creating session',
                'session' => null
            ];
        }
    }

    public function startSession(int $sessionId): ?Session
    {
        try {
            $session = Session::query()->findOrFail($sessionId);
            $session->update([
                'starts_at' => Carbon::now(),
                'status' => QuestionSessionStatus::ONGOING->value,
            ]);

            return $session;
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            return null;
        }
    }

    public function endSession(int $sessionId)
    {
        try {
            $session = Session::query()->findOrFail($sessionId);
            $session->update([
                'ends_at' => Carbon::now(),
                'status' => QuestionSessionStatus::ENDED->value,
            ]);

            return $session;
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            return null;
        }
    }

    public function deleteSession(int $sessionId): bool
    {
        try {
            Session::findOrFail($sessionId)->delete();

            return true;
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            return false;
        }
    }
}
