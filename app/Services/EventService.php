<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Support\Facades\Log;

class EventService
{
    public function switchEvent(int $eventId)
    {
        try {
            if (! $this->getEventById($eventId)) {
                throw new \Exception("Event with ID: $eventId does not exists");
            }
            cache()->forever('current_event_id', $eventId);

            return true;
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            return false;
        }
    }

    public function getUserEvents()
    {
        try {
            return request()->user()->events;
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
        }
    }

    public function getAllEvents()
    {
        try {
            return Event::all();
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

        }
    }

    public function getCurrentEvent(): ?Event
    {
        try {
            $current_event_id = cache()->get('current_event_id') ??
            request()->user()->events()->first()->id;

            return $this->getEventById($current_event_id);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            return null;
        }
    }

    public function getEventById(int $eventId): ?Event
    {
        try {
            return Event::find($eventId);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            return null;
        }
    }

    public function createEvent(array $data): ?Event
    {
        try {
            $event = Event::create($data);
            $event->creator()->associate(request()->user());
            $event->members()->attach(request()->user());

            cache()->forever('current_event_id', $event->id);

            return $event;
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            return null;
        }
    }

    public function updateEvent(Event $event, array $data)
    {
        try {
            $event->update($data);

            return true;
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            return false;
        }
    }
}
