<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Services\EventService;

class EventController extends Controller
{
    public function __construct(
        protected EventService $eventService
    ) {}

    public function store(StoreEventRequest $request)
    {
        $event = $this->eventService->createEvent($request->validated());

        if (! $event) {
            return to_route('dashboard.noevent');
        }

        return to_route('dashboard',[
            'message' => 'event created successully',
            'event' => cache()->get('current_event_id')
        ]);
    }

    public function switchEvent(int $eventId,int $id)
    {
        if (! request()->user()->inEvent($id)) {
            abort(404);
        }
        $eventHasBeenSwitched = $this->eventService->switchEvent($id);

        if ($eventHasBeenSwitched) {
            return redirect()->intended(route('dashboard', ['event' => $id]));
        } else {
            return back()->withErrors([]);
        }
    }
}
