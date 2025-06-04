<?php

use App\Enums\GameStatus;
use App\Models\Game;
use App\Models\Player;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Volt\Component;

/**
 * The EventRunnerVercel component provides a real-time dashboard for managing tabletop gaming events.
 *
 * This component extends the functionality of the base EventRunner component and adds
 * specific features for the Vercel-styled interface.
 */
new class extends Component {
    /**
     * Form properties for adding a new game.
     */
    public string $gameTitle = '';
    public int $gameDuration = 60;
    public string $gameOwner = '';
    public int $maxPlayers = 4;
    public string $gameStatus = 'ready';

    /**
     * Flag to control the visibility of the add game modal.
     */
    public bool $showAddGameModal = false;

    /**
     * Properties for the manage players modal.
     */
    public bool $showManagePlayersModal = false;
    public ?Game $selectedGame = null;
    public array $availablePlayers = [];

    /**
     * Listen for the open-add-game-modal event to show the modal.
     */
    #[\Livewire\Attributes\On('open-add-game-modal')]
    public function openAddGameModal(): void
    {
        $this->showAddGameModal = true;
    }

    /**
     * Close the add game modal.
     */
    public function closeAddGameModal(): void
    {
        $this->showAddGameModal = false;
    }

    /**
     * Add a new game to the event.
     *
     * This method creates a new game with the provided details and adds it to the current event.
     */
    public function addGame(): void
    {
        // Validate the form data
        $this->validate([
            'gameTitle' => 'required|string|min:3|max:255',
            'gameDuration' => 'required|integer|min:15|max:480',
            'gameOwner' => 'required|string|max:255',
            'maxPlayers' => 'required|integer|min:1|max:12',
            'gameStatus' => 'required|in:ready,background',
        ]);

        // Create a new game
        $game = new Game([
            'name' => $this->gameTitle,
            'duration' => $this->gameDuration,
            'game_master' => $this->gameOwner,
            'max_players' => $this->maxPlayers,
            'event_id' => $this->event->id,
            'status' => $this->gameStatus === 'ready' ? GameStatus::Ready : GameStatus::Background,
            'display_order' => Game::where('event_id', $this->event->id)
                ->where('status', GameStatus::Ready)
                ->count() + 1,
        ]);

        $game->save();

        // Reset the form
        $this->reset(['gameTitle', 'gameDuration', 'gameOwner', 'maxPlayers', 'gameStatus']);

        // Close the modal
        $this->closeAddGameModal();

        // Show success notification
        $this->dispatch('success', "Added new game: {$game->name}");
    }

    /**
     * Update a game's status when it's dragged to a different section.
     *
     * This method handles the drag and drop functionality for moving games between sections.
     */
    public function updateGameStatus(int $gameId, string $newStatus): void
    {
        $game = Game::find($gameId);

        if (!$game) {
            return;
        }

        // Store original status for undo functionality
        $oldStatus = $game->status;

        // Map the section ID to a GameStatus
        $statusMap = [
            'currentlyPlaying' => GameStatus::Playing,
            'readyToStart' => GameStatus::Ready,
            'backgroundGames' => GameStatus::Background,
            'finishedGames' => GameStatus::Finished,
        ];

        if (!isset($statusMap[$newStatus])) {
            return;
        }

        // Update the game status
        $game->status = $statusMap[$newStatus];

        // Update timestamps based on status
        if ($game->status === GameStatus::Playing && $oldStatus !== GameStatus::Playing) {
            $game->started_at = now();
        } elseif ($game->status === GameStatus::Finished && $oldStatus !== GameStatus::Finished) {
            $game->finished_at = now();
        }

        $game->save();

        // Dispatch event for action history
        $this->dispatch('gameStatusChanged',
            game: $game,
            oldStatus: $oldStatus,
            newStatus: $game->status
        );

        // Show success notification
        $this->dispatch('success', "Moved {$game->name} to " . $game->status->label());
    }

    /**
     * Open the manage players modal for a specific game.
     *
     * This method prepares the data needed for the manage players modal.
     */
    public function openManagePlayersModal(int $gameId): void
    {
        $this->selectedGame = Game::with('players')->find($gameId);

        if (!$this->selectedGame) {
            return;
        }

        // Get all players in the event who aren't already in this game
        $this->availablePlayers = Player::where('event_id', $this->event->id)
            ->whereNotIn('id', $this->selectedGame->players->pluck('id'))
            ->get()
            ->toArray();

        $this->showManagePlayersModal = true;
    }

    /**
     * Close the manage players modal.
     */
    public function closeManagePlayersModal(): void
    {
        $this->showManagePlayersModal = false;
        $this->selectedGame = null;
        $this->availablePlayers = [];
    }

    /**
     * Add a player to the selected game.
     */
    public function addPlayerToGame(int $playerId): void
    {
        if (!$this->selectedGame) {
            return;
        }

        $player = Player::find($playerId);

        if (!$player) {
            return;
        }

        // Check if the game is at max capacity
        if ($this->selectedGame->players->count() >= ($this->selectedGame->max_players ?? 8)) {
            $this->dispatch('error', "Game is at maximum capacity");
            return;
        }

        // Add the player to the game
        $this->selectedGame->players()->attach($player->id);

        // Refresh the selected game
        $this->selectedGame = Game::with('players')->find($this->selectedGame->id);

        // Remove the player from available players
        $this->availablePlayers = array_filter($this->availablePlayers, function($p) use ($playerId) {
            return $p['id'] !== $playerId;
        });

        // Dispatch event for action history
        $this->dispatch('playerStatusChanged',
            player: $player,
            game: $this->selectedGame,
            isLeft: false
        );

        // Show success notification
        $this->dispatch('success', "Added {$player->display_name} to {$this->selectedGame->name}");
    }

    /**
     * Remove a player from the selected game.
     */
    public function removePlayerFromGame(int $playerId): void
    {
        if (!$this->selectedGame) {
            return;
        }

        $player = Player::find($playerId);

        if (!$player) {
            return;
        }

        // Remove the player from the game
        $this->selectedGame->players()->detach($player->id);

        // Refresh the selected game
        $this->selectedGame = Game::with('players')->find($this->selectedGame->id);

        // Add the player back to available players
        $this->availablePlayers[] = $player->toArray();

        // Dispatch event for action history
        $this->dispatch('playerStatusChanged',
            player: $player,
            game: $this->selectedGame,
            isLeft: true
        );

        // Show success notification
        $this->dispatch('success', "Removed {$player->display_name} from {$this->selectedGame->name}");
    }
}; ?>

<div>
    <style>
        /* Reset and base styles */
        .event-runner * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .event-runner {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }

        .dark .event-runner {
            background-color: #1e1e1e;
            color: #e0e0e0;
        }

        /* Header styles */
        .event-runner .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .event-runner .header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .event-runner .event-selector {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-top: 1rem;
        }

        .event-runner .event-selector select {
            padding: 0.5rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
        }

        .event-runner .event-info {
            background: rgba(255,255,255,0.1);
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-size: 0.9rem;
        }

        .dark .event-runner .event-info {
            background: rgba(255,255,255,0.05);
        }

        /* Main container */
        .event-runner .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Grid layout for sections */
        .event-runner .sections-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            grid-template-rows: auto auto;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .event-runner .section {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .dark .event-runner .section {
            background: #2d2d2d;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .event-runner .section-header {
            background: #f8f9fa;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .dark .event-runner .section-header {
            background: #333333;
            border-bottom: 1px solid #444444;
        }

        .event-runner .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #495057;
        }

        .dark .event-runner .section-title {
            color: #e0e0e0;
        }

        .event-runner .section-count {
            background: #6c757d;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.8rem;
        }

        .dark .event-runner .section-count {
            background: #5a6268;
        }

        .event-runner .section-content {
            padding: 1rem;
            min-height: 200px;
        }

        /* Currently playing section spans full width */
        .event-runner .currently-playing {
            grid-column: 1 / -1;
        }

        .event-runner .currently-playing .section-content {
            min-height: 300px;
        }

        /* Game card styles */
        .event-runner .game-card {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .dark .event-runner .game-card {
            background: #333333;
            border: 1px solid #444444;
        }

        .event-runner .game-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transform: translateY(-1px);
        }

        .dark .event-runner .game-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }

        .event-runner .game-card.playing {
            border-left: 4px solid #28a745;
            background: #f8fff9;
        }

        .dark .event-runner .game-card.playing {
            border-left: 4px solid #28a745;
            background: #1a2e1c;
        }

        .event-runner .game-card.ready {
            border-left: 4px solid #ffc107;
            background: #fffef8;
        }

        .dark .event-runner .game-card.ready {
            border-left: 4px solid #ffc107;
            background: #2e2a1a;
        }

        .event-runner .game-card.finished {
            border-left: 4px solid #6c757d;
            background: #f8f9fa;
        }

        .dark .event-runner .game-card.finished {
            border-left: 4px solid #6c757d;
            background: #2a2a2a;
        }

        .event-runner .game-card.background {
            border-left: 4px solid #17a2b8;
            background: #f8fcfd;
        }

        .dark .event-runner .game-card.background {
            border-left: 4px solid #17a2b8;
            background: #1a2a2e;
        }

        .event-runner .game-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.75rem;
        }

        .event-runner .game-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #212529;
        }

        .dark .event-runner .game-title {
            color: #e0e0e0;
        }

        .event-runner .game-duration {
            font-size: 0.8rem;
            color: #6c757d;
            background: #f8f9fa;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
        }

        .dark .event-runner .game-duration {
            color: #a0a0a0;
            background: #3a3a3a;
        }

        .event-runner .game-meta {
            display: flex;
            gap: 1rem;
            margin-bottom: 0.75rem;
            font-size: 0.9rem;
            color: #6c757d;
        }

        .dark .event-runner .game-meta {
            color: #a0a0a0;
        }

        .event-runner .players-list {
            margin-bottom: 0.75rem;
        }

        .event-runner .players-list h4 {
            font-size: 0.9rem;
            color: #495057;
            margin-bottom: 0.25rem;
        }

        .dark .event-runner .players-list h4 {
            color: #c0c0c0;
        }

        .event-runner .player-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.25rem;
        }

        .event-runner .player-tag {
            background: #e9ecef;
            color: #495057;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.8rem;
        }

        .dark .event-runner .player-tag {
            background: #444444;
            color: #c0c0c0;
        }

        .event-runner .game-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .event-runner .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .event-runner .btn:hover {
            transform: translateY(-1px);
        }

        .event-runner .btn-primary {
            background: #007bff;
            color: white;
        }

        .event-runner .btn-primary:hover {
            background: #0056b3;
        }

        .dark .event-runner .btn-primary {
            background: #0066cc;
        }

        .dark .event-runner .btn-primary:hover {
            background: #0055aa;
        }

        .event-runner .btn-success {
            background: #28a745;
            color: white;
        }

        .event-runner .btn-success:hover {
            background: #1e7e34;
        }

        .dark .event-runner .btn-success {
            background: #218838;
        }

        .dark .event-runner .btn-success:hover {
            background: #1a6e2d;
        }

        .event-runner .btn-warning {
            background: #ffc107;
            color: #212529;
        }

        .event-runner .btn-warning:hover {
            background: #e0a800;
        }

        .dark .event-runner .btn-warning {
            background: #e0a800;
            color: #1a1a1a;
        }

        .dark .event-runner .btn-warning:hover {
            background: #d39e00;
        }

        .event-runner .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .event-runner .btn-secondary:hover {
            background: #545b62;
        }

        .dark .event-runner .btn-secondary {
            background: #5a6268;
        }

        .dark .event-runner .btn-secondary:hover {
            background: #4e555b;
        }

        .event-runner .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }

        /* Points section for finished games */
        .event-runner .points-section {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e9ecef;
        }

        .dark .event-runner .points-section {
            border-top: 1px solid #444444;
        }

        .event-runner .points-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .event-runner .point-input {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .dark .event-runner .point-input {
            color: #c0c0c0;
        }

        .event-runner .point-input input {
            width: 60px;
            padding: 0.25rem;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }

        .dark .event-runner .point-input input {
            background: #333333;
            border: 1px solid #444444;
            color: #e0e0e0;
        }

        /* Modal styles */
        .event-runner .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
        }

        .dark .event-runner .modal {
            background: rgba(0,0,0,0.7);
        }

        .event-runner .modal-content {
            background: white;
            margin: 5% auto;
            padding: 2rem;
            border-radius: 8px;
            max-width: 500px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .dark .event-runner .modal-content {
            background: #2d2d2d;
            color: #e0e0e0;
        }

        .event-runner .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .event-runner .close {
            font-size: 1.5rem;
            cursor: pointer;
            color: #6c757d;
        }

        .dark .event-runner .close {
            color: #a0a0a0;
        }

        .event-runner .form-group {
            margin-bottom: 1rem;
        }

        .event-runner .form-group label {
            display: block;
            margin-bottom: 0.25rem;
            font-weight: 500;
        }

        .dark .event-runner .form-group label {
            color: #c0c0c0;
        }

        .event-runner .form-group input,
        .event-runner .form-group select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }

        .dark .event-runner .form-group input,
        .dark .event-runner .form-group select {
            background: #333333;
            border: 1px solid #444444;
            color: #e0e0e0;
        }

        /* Drag and drop styles */
        .event-runner .game-card.dragging {
            opacity: 0.5;
        }

        .event-runner .section-content.drag-over {
            background: #f8f9fa;
            border: 2px dashed #007bff;
        }

        .dark .event-runner .section-content.drag-over {
            background: #333333;
            border: 2px dashed #0066cc;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .event-runner .sections-grid {
                grid-template-columns: 1fr;
            }

            .event-runner .container {
                padding: 1rem;
            }

            .event-runner .header {
                padding: 1rem;
            }

            .event-runner .event-selector {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        /* Empty state */
        .event-runner .empty-state {
            text-align: center;
            color: #6c757d;
            padding: 2rem;
        }

        .dark .event-runner .empty-state {
            color: #a0a0a0;
        }

        .event-runner .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* Notification styles */
        .event-runner .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 1rem;
            border-radius: 4px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1001;
            transform: translateX(100%);
            transition: transform 0.3s ease;
        }

        .dark .event-runner .notification {
            background: #218838;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }

        .event-runner .notification.show {
            transform: translateX(0);
        }
    </style>

    <div class="event-runner">
        <!-- Header Section -->
        <header class="header">
            <h1>Event Runner</h1>
            <p>Real-time tabletop gaming event management</p>

            <div class="event-selector">
                <label for="eventSelect">Current Event:</label>
                <select id="eventSelect" wire:model="selectedEventId" wire:change="selectEvent($event.target.value)">
                    @foreach($groupedEvents['ongoing'] as $ongoingEvent)
                        <option value="{{ $ongoingEvent->id }}" {{ $event->id === $ongoingEvent->id ? 'selected' : '' }}>
                            {{ $ongoingEvent->name }}
                        </option>
                    @endforeach
                    @foreach($groupedEvents['upcoming'] as $upcomingEvent)
                        <option value="{{ $upcomingEvent->id }}">
                            {{ $upcomingEvent->name }}
                        </option>
                    @endforeach
                    @foreach($groupedEvents['past'] as $pastEvent)
                        <option value="{{ $pastEvent->id }}">
                            {{ $pastEvent->name }}
                        </option>
                    @endforeach
                </select>
                <div class="event-info">
                    {{ $event->start_date?->format('M d') ?? 'N/A' }} - {{ $event->end_date?->format('M d, Y') ?? 'N/A' }} •
                    {{ $event->players_count ?? 0 }} Players •
                    {{ $event->games_count ?? 0 }} Games
                </div>
            </div>
        </header>

        <!-- Main Container -->
        <div class="container">
            <!-- Action Buttons -->
            <div style="margin-bottom: 2rem;">
                <button class="btn btn-primary" x-data x-on:click="$dispatch('open-add-game-modal')">Add New Game</button>
                <button class="btn btn-secondary" wire:click="$refresh">Refresh</button>
            </div>

            <!-- Main Sections Grid -->
            <div class="sections-grid">
                <!-- Currently Playing Section -->
                <section class="section currently-playing">
                    <div class="section-header">
                        <h2 class="section-title">Currently Playing</h2>
                        <span class="section-count" id="playingCount">{{ $currentlyPlayingGames->count() }}</span>
                    </div>
                    <div class="section-content" id="currentlyPlaying">
                        @if($currentlyPlayingGames->count() === 0)
                            <div class="empty-state">No games currently playing</div>
                        @else
                            @foreach($currentlyPlayingGames as $game)
                                <div class="game-card playing" draggable="true" ondragstart="drag(event)" data-game-id="{{ $game->id }}">
                                    <div class="game-header">
                                        <div class="game-title">{{ $game->name }}</div>
                                        <div class="game-duration">Playing for {{ $game->started_at?->diffForHumans(null, true) ?? 'a while' }}</div>
                                    </div>
                                    <div class="game-meta">
                                        <span>👤 {{ $game->players->count() }} players</span>
                                        <span>🎮 {{ $game->game_master }}</span>
                                        <span>⏱️ {{ $game->duration }} min planned</span>
                                    </div>
                                    <div class="players-list">
                                        <h4>Players:</h4>
                                        <div class="player-tags">
                                            @foreach($game->players as $player)
                                                <span class="player-tag">{{ $player->display_name }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="game-actions">
                                        <button class="btn btn-success btn-sm" wire:click="finishGame({{ $game->id }})">Finish Game</button>
                                        <button class="btn btn-secondary btn-sm" wire:click="openManagePlayersModal({{ $game->id }})">Manage Players</button>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </section>

                <!-- Ready to Start Section -->
                <section class="section">
                    <div class="section-header">
                        <h2 class="section-title">Ready to Start</h2>
                        <span class="section-count" id="readyCount">{{ $readyToStartGames->count() }}</span>
                    </div>
                    <div class="section-content" id="readyToStart" ondrop="drop(event)" ondragover="allowDrop(event)">
                        @if($readyToStartGames->count() === 0)
                            <div class="empty-state">No games ready to start</div>
                        @else
                            @foreach($readyToStartGames as $game)
                                <div class="game-card ready" draggable="true" ondragstart="drag(event)" data-game-id="{{ $game->id }}">
                                    <div class="game-header">
                                        <div class="game-title">{{ $game->name }}</div>
                                        <div class="game-duration">{{ $game->duration }} min</div>
                                    </div>
                                    <div class="game-meta">
                                        <span>👤 {{ $game->players->count() }} players</span>
                                        <span>🎮 {{ $game->game_master }}</span>
                                    </div>
                                    <div class="players-list">
                                        <h4>Players:</h4>
                                        <div class="player-tags">
                                            @foreach($game->players as $player)
                                                <span class="player-tag">{{ $player->display_name }}</span>
                                            @endforeach
                                            @if($game->players->count() < ($game->max_players ?? 4))
                                                <span class="player-tag" style="background: #e3f2fd; color: #1976d2; cursor: pointer;">+ Add Player</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="game-actions">
                                        <button class="btn btn-primary btn-sm" wire:click="startGame({{ $game->id }})" {{ $game->players->count() === 0 ? 'disabled' : '' }}>Start Game</button>
                                        <button class="btn btn-secondary btn-sm" wire:click="openManagePlayersModal({{ $game->id }})">Manage Players</button>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </section>

                <!-- Background Games Section -->
                <section class="section">
                    <div class="section-header">
                        <h2 class="section-title">Background Games</h2>
                        <span class="section-count" id="backgroundCount">{{ $backgroundGames->count() }}</span>
                    </div>
                    <div class="section-content" id="backgroundGames">
                        @if($backgroundGames->count() === 0)
                            <div class="empty-state">No background games</div>
                        @else
                            @foreach($backgroundGames as $game)
                                <div class="game-card background" data-game-id="{{ $game->id }}">
                                    <div class="game-header">
                                        <div class="game-title">{{ $game->name }}</div>
                                        <div class="game-duration">{{ $game->schedule ?? 'All convention' }}</div>
                                    </div>
                                    <div class="game-meta">
                                        <span>👤 {{ $game->players->count() }} players</span>
                                        <span>🎮 {{ $game->game_master }}</span>
                                    </div>
                                    <div class="players-list">
                                        <h4>Players:</h4>
                                        <div class="player-tags">
                                            @foreach($game->players->take(3) as $player)
                                                <span class="player-tag">{{ $player->display_name }}</span>
                                            @endforeach
                                            @if($game->players->count() > 3)
                                                <span class="player-tag">+{{ $game->players->count() - 3 }} more</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="game-actions">
                                        <button class="btn btn-success btn-sm" wire:click="finishGame({{ $game->id }})">Finish Game</button>
                                        <button class="btn btn-secondary btn-sm" wire:click="openManagePlayersModal({{ $game->id }})">Manage Players</button>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </section>
            </div>

            <!-- Finished Games Section -->
            <section class="section">
                <div class="section-header">
                    <h2 class="section-title">Recently Finished</h2>
                    <span class="section-count" id="finishedCount">{{ $finishedGames->count() }}</span>
                </div>
                <div class="section-content" id="finishedGames">
                    @if($finishedGames->count() === 0)
                        <div class="empty-state">No finished games yet</div>
                    @else
                        @foreach($finishedGames as $game)
                            <div class="game-card finished" data-game-id="{{ $game->id }}">
                                <div class="game-header">
                                    <div class="game-title">{{ $game->name }}</div>
                                    <div class="game-duration">Finished {{ $game->finished_at?->diffForHumans() ?? 'recently' }}</div>
                                </div>
                                <div class="game-meta">
                                    <span>👤 {{ $game->players->count() }} players</span>
                                    <span>🎮 {{ $game->game_master }}</span>
                                    <span>⏱️ {{ $game->duration }} min</span>
                                </div>
                                <div class="players-list">
                                    <h4>Final Results:</h4>
                                    <div class="player-tags">
                                        @foreach($game->players->sortByDesc('score')->values() as $index => $player)
                                            <span class="player-tag">{{ $index + 1 }}. {{ $player->display_name }} ({{ $player->score ?? 0 }}pts)</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </section>
        </div>

        <!-- Add Game Modal -->
        <div x-data x-show="$wire.showAddGameModal" class="modal" style="display: none;" x-transition.opacity>
            <div class="modal-content" @click.outside="$wire.closeAddGameModal()">
                <div class="modal-header">
                    <h3>Add New Game</h3>
                    <span class="close" wire:click="closeAddGameModal">&times;</span>
                </div>
                <form wire:submit.prevent="addGame">
                    <div class="form-group">
                        <label for="gameTitle">Game Title</label>
                        <input type="text" id="gameTitle" wire:model="gameTitle" required>
                        @error('gameTitle') <div class="text-red-500 mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="gameDuration">Duration (minutes)</label>
                        <input type="number" id="gameDuration" wire:model="gameDuration" min="15" max="480">
                        @error('gameDuration') <div class="text-red-500 mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="gameOwner">Game Owner</label>
                        <select id="gameOwner" wire:model="gameOwner">
                            <option value="">Select Game Owner</option>
                            <option value="Alice Johnson">Alice Johnson</option>
                            <option value="Bob Smith">Bob Smith</option>
                            <option value="Carol Davis">Carol Davis</option>
                            <option value="David Wilson">David Wilson</option>
                        </select>
                        @error('gameOwner') <div class="text-red-500 mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="maxPlayers">Maximum Players</label>
                        <input type="number" id="maxPlayers" wire:model="maxPlayers" min="1" max="12">
                        @error('maxPlayers') <div class="text-red-500 mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="gameStatus">Initial Status</label>
                        <select id="gameStatus" wire:model="gameStatus">
                            <option value="ready">Ready to Start</option>
                            <option value="background">Background Game</option>
                        </select>
                        @error('gameStatus') <div class="text-red-500 mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                        <button type="button" class="btn btn-secondary" wire:click="closeAddGameModal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Game</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Manage Players Modal -->
        <div x-data x-show="$wire.showManagePlayersModal" class="modal" style="display: none;" x-transition.opacity>
            <div class="modal-content" @click.outside="$wire.closeManagePlayersModal()">
                <div class="modal-header">
                    <h3>Manage Players: {{ $selectedGame?->name ?? 'Game' }}</h3>
                    <span class="close" wire:click="closeManagePlayersModal">&times;</span>
                </div>
                <div>
                    @if($selectedGame)
                        <div class="mb-4">
                            <h4 class="text-lg font-semibold mb-2">Current Players</h4>
                            @if($selectedGame->players->count() > 0)
                                <div class="space-y-2">
                                    @foreach($selectedGame->players as $player)
                                        <div class="flex justify-between items-center p-2 bg-gray-100 dark:bg-gray-700 rounded">
                                            <span>{{ $player->display_name }}</span>
                                            <button
                                                class="btn btn-sm btn-secondary"
                                                wire:click="removePlayerFromGame({{ $player->id }})"
                                                wire:loading.attr="disabled"
                                            >
                                                Remove
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 dark:text-gray-400">No players in this game yet.</p>
                            @endif
                        </div>

                        <div>
                            <h4 class="text-lg font-semibold mb-2">Available Players</h4>
                            @if(count($availablePlayers) > 0)
                                <div class="space-y-2">
                                    @foreach($availablePlayers as $player)
                                        <div class="flex justify-between items-center p-2 bg-gray-100 dark:bg-gray-700 rounded">
                                            <span>{{ $player['display_name'] }}</span>
                                            <button
                                                class="btn btn-sm btn-primary"
                                                wire:click="addPlayerToGame({{ $player['id'] }})"
                                                wire:loading.attr="disabled"
                                            >
                                                Add
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 dark:text-gray-400">No more players available in this event.</p>
                            @endif
                        </div>
                    @else
                        <p class="text-center py-4">No game selected.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Notification Container -->
        <div id="notification" class="notification"></div>
    </div>

    <script>
        // Functions for drag and drop
        function allowDrop(ev) {
            ev.preventDefault();
            ev.currentTarget.classList.add('drag-over');
        }

        function drag(ev) {
            ev.dataTransfer.setData("text", ev.target.getAttribute('data-game-id'));
            ev.target.classList.add('dragging');
        }

        function drop(ev) {
            ev.preventDefault();
            ev.currentTarget.classList.remove('drag-over');

            const gameId = parseInt(ev.dataTransfer.getData("text"));
            const targetSection = ev.currentTarget.id;

            // Call the Livewire method to update the game status
            Livewire.find('{{ $_instance->getId() }}').updateGameStatus(gameId, targetSection);
        }

        // Show notification function
        function showNotification(message) {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.classList.add('show');

            setTimeout(() => {
                notification.classList.remove('show');
            }, 3000);
        }

        // Listen for Livewire events
        document.addEventListener('livewire:initialized', () => {
            // Listen for success notifications
            Livewire.on('success', (message) => {
                showNotification(message);
            });

            // Listen for error notifications
            Livewire.on('error', (message) => {
                showNotification(message);
                // Add error styling
                const notification = document.getElementById('notification');
                notification.style.backgroundColor = '#dc3545';
            });
        });

        // Remove dragging class when drag ends
        document.addEventListener('dragend', function(e) {
            e.target.classList.remove('dragging');
            document.querySelectorAll('.drag-over').forEach(el => {
                el.classList.remove('drag-over');
            });
        });
    </script>
</div>
