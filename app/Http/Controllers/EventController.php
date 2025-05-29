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

        // Get current active game (most recent)
        $currentGame = $event->games()->latest()->first();

        // Calculate game duration if there's a current game and the event has started
        $gameDuration = null;
        if ($currentGame && $event->started_at) {
            $gameDuration = now()->diffForHumans($event->started_at, true);
        }

        // Get finished games for the event (excluding the current game)
        $finishedGames = $event->games()
            ->when($currentGame, function ($query) use ($currentGame) {
                return $query->where('id', '!=', $currentGame->id);
            })
            ->whereNotNull('created_at')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get upcoming games for the event
        // Since there's no specific field to determine if a game is upcoming,
        // we'll assume games without a created_at timestamp are upcoming
        $upcomingGames = $event->games()
            ->whereNull('created_at')
            ->orderBy('id', 'asc')
            ->take(5)
            ->get();

        return view('events.show', compact('event', 'currentGame', 'gameDuration', 'finishedGames', 'upcomingGames'));
    }

    public function update(EventRequest $request, Event $event)
    {
        $this->authorize('update', $event);

        $event->update($request->validated());

        // Check if the request is coming from the dashboard
        if ($request->header('Referer') && str_contains($request->header('Referer'), '/dashboard')) {
            return redirect()->route('dashboard')->with('success', 'Event updated successfully.');
        }

        return new EventResource($event);
    }

    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);

        $event->delete();

        return response()->json();
    }
}
