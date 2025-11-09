<?php

namespace App\Services;

use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubjectService
{
    public function __construct(
        protected EventService $eventService,
    ) {}

    public function showSubject(Subject $subject)
    {
        try {
            return $subject->load(['topics', 'questions.topic']);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            return null;
        }

    }

    public function getAllSubjects()
    {
        try {
            return $this->eventService
                ->getCurrentEvent()
                ->subjects()->with('topics')->get();
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
        }
    }

    public function addSubject(array $data): ?Subject
    {
        try {
            return DB::transaction(function () use ($data): Subject {
                $event = $this->eventService->getCurrentEvent();

                /** @var Subject $subject */
                $subject = $event->subjects()->create(
                    collect($data)->except('topics')->toArray()
                );

                if (! empty($data['topics'])) {
                    $subject->topics()->createMany(
                        collect($data['topics'])
                            ->filter(fn ($topic) => ! empty($topic))
                            ->map(fn (string $topicName) => ['name' => $topicName])
                            ->values()
                            ->all()
                    );
                }

                return $subject;
            });
        } catch (\Throwable $th) {
            Log::error($th->getMessage());

            return null;
        }
    }

    public function updateSubject(Subject $subject, array $data): bool
    {
        try {
            DB::transaction(function () use ($subject, $data) {
                $subject->update(collect($data)->except('topics')->toArray());

                if (! empty($data['topics'])) {

                    foreach ($data['topics'] as $topic) {
                        $existingTopic = $subject->topics()->find($topic['id']);
                        if ($existingTopic) {
                            $existingTopic->update($topic);
                        } else {
                            $subject->topics()->create([
                                'name' => $topic['name'],
                            ]);
                        }
                    }
                }
            });

            return true;
        } catch (\Throwable $th) {
            Log::error($th->getMessage());

            return false;
        }
    }

    public function deleteSubject(Subject $subject): bool
    {
        try {
            DB::transaction(function () use ($subject): void {
                $subject->topics()->delete();
                $subject->delete();
            });

            return true;
        } catch (\Throwable $th) {
            Log::error($th->getMessage());

            return false;
        }
    }
}
