<?php

namespace App\Services;

use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Support\Facades\Log;

class TopicService
{
    public function __construct(
        protected EventService $eventService,
    ) {}

    public function getAllTopics()
    {
        try {
            return $this->eventService
                ->getCurrentEvent()
                ->topics()
                ->with('subject')
                ->orderBy('name', 'asc')
                ->get();
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
        }
    }

    public function addTopic(array $data, Subject $subject): ?Topic
    {
        try {
            $topic = $subject->topics()->create($data);

            return $topic;
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            return null;
        }
    }

    public function updateTopic(Topic $topic, array $data): bool
    {
        try {
            $topic->update($data);

            return true;
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            return false;
        }
    }

    public function deleteTopic(Topic $topic): bool
    {
        try {
            $topic->delete();

            return true;
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            return false;
        }
    }
}
