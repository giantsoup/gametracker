<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GameController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the games.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Game::class);

        return view('games.index');
    }

    /**
     * Show the form for creating a new game.
     */
    public function create(): View
    {
        $this->authorize('create', Game::class);

        return view('games.create');
    }

    /**
     * Store a newly created game in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Game::class);

        // Validation and storage logic would go here
        return redirect()->route('games.index');
    }

    /**
     * Display the specified game.
     */
    public function show(Game $game): View
    {
        $this->authorize('view', $game);

        return view('games.show', compact('game'));
    }

    /**
     * Show the form for editing the specified game.
     */
    public function edit(Game $game): View
    {
        $this->authorize('update', $game);

        return view('games.edit', compact('game'));
    }

    /**
     * Update the specified game in storage.
     */
    public function update(Request $request, Game $game)
    {
        $this->authorize('update', $game);

        // Validation and update logic would go here
        return redirect()->route('games.index');
    }

    /**
     * Remove the specified game from storage.
     */
    public function destroy(Game $game)
    {
        $this->authorize('delete', $game);

        // Delete logic would go here
        return redirect()->route('games.index');
    }
}
