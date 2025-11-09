<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserMustBeInEvent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $eventId = (int) $request->route('event') ?? (int) $request->route('eventId');
        if ($request->user()->inEvent($eventId)) {
            cache()->set('current_event_id', $eventId);

            return $next($request);
        }
        abort(404);
    }
}
