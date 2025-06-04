<?php

namespace App\Livewire;

use App\Enums\GameStatus;
use App\Models\Event;
use App\Models\Game;
use App\Models\GamePoint;
use App\Models\Player;
use Illuminate\Http\Request;
use Livewire\Component;

class LoggedInDashboard extends Component
{
    public $activeEventId = null; // Currently selected event ID

    public $events = []; // All active events

    // Event editing properties
    public $isEditingEvent = false;

    public $editingEvent = null;

    public $eventName = '';

    public $eventStartedAt = '';

    public $eventEndsAt = '';

    public $eventActive = true;

    public function mount(Request $request)
    {
        // Get all active events
        $this->events = Event::active()->get();

        // Check if event is specified in query parameters
        if ($request->has('event')) {
            $eventId = (int) $request->input('event');
            // Check if the event exists in our active events
            if ($this->events->contains('id', $eventId)) {
                $this->activeEventId = $eventId;
            }
        }

        // If no event is selected or the selected event doesn't exist, use the first active event
        if (! $this->activeEventId && $this->events->isNotEmpty()) {
            $this->activeEventId = $this->events->first()->id;
        }
    }

    public function render()
    {
        // Get all active events
        $events = $this->events;

        // Get the current active event
        $activeEvent = $events->firstWhere('id', $this->activeEventId);

        // If no active event is found, use the first one (if available)
        if (! $activeEvent && $events->isNotEmpty()) {
            $activeEvent = $events->first();
            $this->activeEventId = $activeEvent->id;
        }

        // Get current active game if there is an active event
        $currentGame = null;
        $gameDuration = null;
        $finishedGames = collect();
        $upcomingGames = collect();

        if ($activeEvent) {
            // Get the current active game (most recent)
            $currentGame = $activeEvent->games()->latest()->first();

            if ($currentGame) {
                // Calculate game duration based on the game's timing information
                $gameDuration = $currentGame->getTotalDurationForHumans();
            }

            // Get finished games for the current event (excluding the current game)
            $finishedGames = $activeEvent->games()
                ->when($currentGame, function ($query) use ($currentGame) {
                    return $query->where('id', '!=', $currentGame->id);
                })
                ->whereNotNull('created_at')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            // Get upcoming games for the current event
            // Since there's no specific field to determine if a game is upcoming,
            // we'll assume games without a created_at timestamp are upcoming
            $upcomingGames = $activeEvent->games()
                ->whereNull('created_at')
                ->orderBy('id', 'asc')
                ->take(5)
                ->get();
        }

        return view('livewire.logged-in-dashboard', [
            'events' => $events,
            'activeEvent' => $activeEvent,
            'currentGame' => $currentGame,
            'gameDuration' => $gameDuration,
            'finishedGames' => $finishedGames,
            'upcomingGames' => $upcomingGames,
        ]);
    }

    public function switchEvent($eventId)
    {
        // Check if the event exists in our active events
        if ($this->events->contains('id', $eventId)) {
            $this->activeEventId = $eventId;
        }
    }

    // Event editing methods
    public function startEditingEvent($eventId)
    {
        $event = Event::find($eventId);
        if (! $event) {
            return;
        }

        $this->isEditingEvent = true;
        $this->editingEvent = $event;
        $this->eventName = $event->name;
        $this->eventStartedAt = $event->started_at ? $event->started_at->format('Y-m-d\TH:i') : '';
        $this->eventEndsAt = $event->ends_at ? $event->ends_at->format('Y-m-d\TH:i') : '';
        $this->eventActive = $event->active;
    }

    public function cancelEditEvent()
    {
        $this->resetEventForm();
    }

    public function saveEvent()
    {
        $this->validate([
            'eventName' => 'required|string|max:255',
            'eventStartedAt' => 'nullable|date',
            'eventEndsAt' => 'nullable|date|after_or_equal:eventStartedAt',
        ]);

        $this->editingEvent->update([
            'name' => $this->eventName,
            'started_at' => $this->eventStartedAt,
            'ends_at' => $this->eventEndsAt,
            'active' => $this->eventActive,
        ]);

        $this->resetEventForm();

        // Refresh the events list
        $this->events = Event::active()->get();
    }

    private function resetEventForm()
    {
        $this->isEditingEvent = false;
        $this->editingEvent = null;
        $this->eventName = '';
        $this->eventStartedAt = '';
        $this->eventEndsAt = '';
        $this->eventActive = true;
    }

    // Game editing properties and methods
    public $isEditingGame = false;

    public $editingGame = null;

    public $gameName = '';

    public $gameDurationHours = 0;

    public $gameDurationMinutes = 0;

    public function startEditingGame($gameId)
    {
        $game = Game::find($gameId);
        if (! $game) {
            return;
        }

        $this->isEditingGame = true;
        $this->editingGame = $game;
        $this->gameName = $game->name;
        $this->gameDurationHours = floor($game->duration / 60);
        $this->gameDurationMinutes = $game->duration % 60;
    }

    public function cancelEditGame()
    {
        $this->resetGameForm();
    }

    public function saveGame()
    {
        $this->validate([
            'gameName' => 'required|string|max:255',
            'gameDurationHours' => 'required|integer|min:0',
            'gameDurationMinutes' => 'required|integer|min:0|max:59',
        ]);

        $duration = ($this->gameDurationHours * 60) + $this->gameDurationMinutes;

        $this->editingGame->update([
            'name' => $this->gameName,
            'duration' => $duration,
        ]);

        $this->resetGameForm();
    }

    private function resetGameForm()
    {
        $this->isEditingGame = false;
        $this->editingGame = null;
        $this->gameName = '';
        $this->gameDurationHours = 0;
        $this->gameDurationMinutes = 0;
    }

    // Player editing properties and methods
    public $isEditingPlayer = false;

    public $editingPlayer = null;

    public $playerNickname = '';

    public $playerJoinedAt = '';

    public $playerLeftAt = '';

    public $selectedPlayerId = null;

    public $availablePlayers = [];

    public function startEditingPlayer($playerId)
    {
        $player = Player::find($playerId);
        if (! $player) {
            return;
        }

        $this->isEditingPlayer = true;
        $this->editingPlayer = $player;
        $this->playerNickname = $player->nickname;
        $this->playerJoinedAt = $player->joined_at ? $player->joined_at->format('Y-m-d\TH:i') : '';
        $this->playerLeftAt = $player->left_at ? $player->left_at->format('Y-m-d\TH:i') : '';
    }

    public function cancelEditPlayer()
    {
        $this->resetPlayerForm();
    }

    public function savePlayer()
    {
        $this->validate([
            'playerNickname' => 'nullable|string|max:255',
            'playerJoinedAt' => 'nullable|date',
            'playerLeftAt' => 'nullable|date|after_or_equal:playerJoinedAt',
        ]);

        $this->editingPlayer->update([
            'nickname' => $this->playerNickname,
            'joined_at' => $this->playerJoinedAt,
            'left_at' => $this->playerLeftAt,
        ]);

        $this->resetPlayerForm();
    }

    public function addPlayerToGame($gameId)
    {
        $this->validate([
            'selectedPlayerId' => 'required|exists:players,id',
        ]);

        $game = Game::find($gameId);
        if (! $game) {
            return;
        }

        $player = Player::find($this->selectedPlayerId);
        if (! $player) {
            return;
        }

        // Add player to the game's players relationship to mark them as playing
        $statusKey = 'status_in_game_'.$game->id;
        if ($player->$statusKey === 'not_playing') {
            $game->players()->attach($player->id);

            // Flash a success message
            session()->flash('message', $player->display_name.' added to the game successfully.');
            session()->flash('message-type', 'success');
        }

        $this->selectedPlayerId = null;
    }

    /**
     * Add a player directly to a game without using the dropdown.
     * This is used from the "Available Players" table.
     */
    public function addPlayerDirectlyToGame($gameId, $playerId)
    {
        $game = Game::find($gameId);
        if (! $game) {
            return;
        }

        $player = Player::find($playerId);
        if (! $player) {
            return;
        }

        // Add player to the game's players relationship to mark them as playing
        $statusKey = 'status_in_game_'.$game->id;
        if ($player->$statusKey === 'not_playing') {
            $game->players()->attach($player->id);

            // Flash a success message
            session()->flash('message', $player->display_name.' added to the game successfully.');
            session()->flash('message-type', 'success');
        }
    }

    public function removePlayerFromGame($gameId, $playerId)
    {
        $game = Game::find($gameId);
        if (! $game) {
            return;
        }

        $player = Player::find($playerId);
        if (! $player) {
            return;
        }

        // Remove player from owners relationship
        $game->owners()->detach($playerId);

        // Also remove player from players relationship
        $game->players()->detach($playerId);

        // Flash a success message
        session()->flash('message', $player->display_name.' removed from the game.');
        session()->flash('message-type', 'success');
    }

    public function loadAvailablePlayers($eventId)
    {
        $this->availablePlayers = Player::where('event_id', $eventId)->get();
    }

    /**
     * Mark a player as having left a game.
     */
    public function markPlayerLeftGame($gameId, $playerId)
    {
        $game = Game::find($gameId);
        $player = Player::find($playerId);

        if (! $game || ! $player) {
            return;
        }

        if ($game->markPlayerLeft($player)) {
            // Flash a success message
            session()->flash('message', $player->display_name.' marked as left the game.');
            session()->flash('message-type', 'success');
        }
    }

    /**
     * Mark a player as active in a game (not left).
     */
    public function markPlayerActiveInGame($gameId, $playerId)
    {
        $game = Game::find($gameId);
        $player = Player::find($playerId);

        if (! $game || ! $player) {
            return;
        }

        if ($game->markPlayerActive($player)) {
            // Flash a success message
            session()->flash('message', $player->display_name.' marked as playing again.');
            session()->flash('message-type', 'success');
        }
    }

    private function resetPlayerForm()
    {
        $this->isEditingPlayer = false;
        $this->editingPlayer = null;
        $this->playerNickname = '';
        $this->playerJoinedAt = '';
        $this->playerLeftAt = '';
        $this->selectedPlayerId = null;
    }

    // Points editing properties and methods
    public $isEditingPoints = false;

    public $editingPoints = null;

    public $pointsValue = 0;

    public $pointsPlacement = 1;

    public function startEditingPoints($pointsId)
    {
        $points = GamePoint::find($pointsId);
        if (! $points) {
            return;
        }

        $this->isEditingPoints = true;
        $this->editingPoints = $points;
        $this->pointsValue = $points->points;
        $this->pointsPlacement = $points->placement;
    }

    public function cancelEditPoints()
    {
        $this->resetPointsForm();
    }

    public function savePoints()
    {
        $this->validate([
            'pointsValue' => 'required|integer|min:0',
            'pointsPlacement' => 'required|integer|min:1',
        ]);

        // Check if this is a new record or an existing one
        if ($this->editingPoints->exists) {
            // Update existing record
            $this->editingPoints->update([
                'points' => $this->pointsValue,
                'placement' => $this->pointsPlacement,
                'last_modified_by' => auth()->id(),
                'last_modified_at' => now(),
            ]);
        } else {
            // Create new record
            GamePoint::create([
                'game_id' => $this->editingPoints->game_id,
                'player_id' => $this->editingPoints->player_id,
                'points' => $this->pointsValue,
                'placement' => $this->pointsPlacement,
                'assigned_by' => auth()->id(),
                'assigned_at' => now(),
            ]);
        }

        $this->resetPointsForm();
    }

    public function addPointsToPlayer($gameId, $playerId)
    {
        // Set default values if not already set
        if ($this->pointsValue === 0 && $this->pointsPlacement === 1) {
            // Show a form to enter points instead of using defaults
            $this->isEditingPoints = true;
            $this->editingPoints = new GamePoint([
                'game_id' => $gameId,
                'player_id' => $playerId,
                'points' => 0,
                'placement' => 1,
            ]);

            return;
        }

        $this->validate([
            'pointsValue' => 'required|integer|min:0',
            'pointsPlacement' => 'required|integer|min:1',
        ]);

        GamePoint::create([
            'game_id' => $gameId,
            'player_id' => $playerId,
            'points' => $this->pointsValue,
            'placement' => $this->pointsPlacement,
            'assigned_by' => auth()->id(),
            'assigned_at' => now(),
        ]);

        $this->resetPointsForm();
    }

    private function resetPointsForm()
    {
        $this->isEditingPoints = false;
        $this->editingPoints = null;
        $this->pointsValue = 0;
        $this->pointsPlacement = 1;
    }

    /**
     * Quick assign points to players based on their order in the game.
     * This uses the default points distribution from the game.
     */
    public function quickAssignPoints($gameId)
    {
        $game = Game::find($gameId);
        if (! $game) {
            return;
        }

        // Get the points distribution from the game or use the default
        $distribution = $game->points_distribution ?? Game::getDefaultPointsDistribution();

        // Sort by placement (key)
        ksort($distribution);

        // Get all owners of the game
        $owners = $game->owners;

        // If there are no owners, return
        if ($owners->isEmpty()) {
            return;
        }

        // Assign points to each owner based on their position in the owners collection
        foreach ($owners as $index => $owner) {
            // Placement is 1-based, so add 1 to the index
            $placement = $index + 1;

            // Get points for this placement from the distribution, or 0 if not defined
            $points = $distribution[$placement] ?? 0;

            // Check if points already exist for this player
            $gamePoint = $game->points()->where('player_id', $owner->id)->first();

            if ($gamePoint) {
                // Update existing points
                $gamePoint->update([
                    'points' => $points,
                    'placement' => $placement,
                    'last_modified_by' => auth()->id(),
                    'last_modified_at' => now(),
                ]);
            } else {
                // Create new points
                GamePoint::create([
                    'game_id' => $game->id,
                    'player_id' => $owner->id,
                    'points' => $points,
                    'placement' => $placement,
                    'assigned_by' => auth()->id(),
                    'assigned_at' => now(),
                ]);
            }
        }

        // Set game status to Played
        $game->setStatus(GameStatus::Played);
    }

    /**
     * Finalize a game by marking it as played and prompting for points assignment.
     */
    public function finalizeGame($gameId)
    {
        $game = Game::find($gameId);
        if (! $game) {
            return;
        }

        // Stop the game if it's running
        if ($game->isRunning()) {
            $game->stopGame();
        }

        // Set game status to Played
        $game->setStatus(GameStatus::Played);

        // Quick assign points
        $this->quickAssignPoints($gameId);
    }

    // Game status and timer methods
    public function startGame($gameId)
    {
        $game = Game::find($gameId);
        if (! $game) {
            return;
        }

        $game->startGame();
    }

    public function stopGame($gameId)
    {
        $game = Game::find($gameId);
        if (! $game) {
            return;
        }

        $game->stopGame();
    }

    public function setGameStatus($gameId, $status)
    {
        $game = Game::find($gameId);
        if (! $game) {
            return;
        }

        switch ($status) {
            case 'unplayed':
                $game->setStatus(GameStatus::Unplayed);
                break;
            case 'active':
                $game->setStatus(GameStatus::Active);
                break;
            case 'played':
                $game->setStatus(GameStatus::Played);
                break;
        }
    }

    // New game creation
    public $isCreatingGame = false;

    public $newGameName = '';

    public $newGameDurationHours = 0;

    public $newGameDurationMinutes = 30;

    public function startCreatingGame()
    {
        $this->isCreatingGame = true;
        $this->newGameName = '';
        $this->newGameDurationHours = 0;
        $this->newGameDurationMinutes = 30;
    }

    public function cancelCreateGame()
    {
        $this->isCreatingGame = false;
        $this->newGameName = '';
        $this->newGameDurationHours = 0;
        $this->newGameDurationMinutes = 30;
    }

    public function createGame()
    {
        $this->validate([
            'newGameName' => 'required|string|max:255',
            'newGameDurationHours' => 'required|integer|min:0',
            'newGameDurationMinutes' => 'required|integer|min:0|max:59',
        ]);

        $duration = ($this->newGameDurationHours * 60) + $this->newGameDurationMinutes;

        Game::create([
            'name' => $this->newGameName,
            'duration' => $duration,
            'event_id' => $this->activeEventId,
            'status' => GameStatus::Unplayed,
        ]);

        $this->isCreatingGame = false;
        $this->newGameName = '';
        $this->newGameDurationHours = 0;
        $this->newGameDurationMinutes = 30;
    }
}
