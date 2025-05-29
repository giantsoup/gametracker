<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class EventController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Event::class);

        $events = Event::all();

        return view('events.index', compact('events'));
    }

    public function store(EventRequest $request)
    {
        $this->authorize('create', Event::class);

        $event = Event::create($request->validated());

        return redirect()->route('events.index')->with('success', 'Event created successfully.');
    }

    public function create()
    {
        $this->authorize('create', Event::class);

        return view('events.create');
    }

    public function show(Event $event)
    {
        $this->authorize('view', $event);

        return view('events.show', compact('event'));
    }

    public function update(EventRequest $request, Event $event)
    {
        $this->authorize('update', $event);

        $event->update($request->validated());

        return new EventResource($event);
    }

    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);

        $event->delete();

        return response()->json();
    }
}
