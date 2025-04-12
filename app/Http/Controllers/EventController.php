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

        return EventResource::collection(Event::all());
    }

    public function store(EventRequest $request)
    {
        $this->authorize('create', Event::class);

        return new EventResource(Event::create($request->validated()));
    }

    public function show(Event $event)
    {
        $this->authorize('view', $event);

        return new EventResource($event);
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
