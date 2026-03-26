<?php

use App\Models\Event;
use App\Models\Game;
use App\Models\GamePoint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('game show page renders flux modal wiring for assigning and editing points', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create();
    $game = Game::factory()->create([
        'event_id' => $event->id,
    ]);
    $player = User::factory()->create();

    $gamePoint = GamePoint::create([
        'game_id' => $game->id,
        'player_id' => $player->id,
        'points' => 5,
        'placement' => 1,
        'assigned_by' => $user->id,
        'assigned_at' => now(),
    ]);

    $response = $this
        ->actingAs($user)
        ->get(route('games.show', $game));

    $response->assertSuccessful();
    $response->assertSee('Assign Points');
    $response->assertSee('data-modal="assign-points-modal"', false);
    $response->assertSee('data-modal="modify-points-modal-'.$gamePoint->id.'"', false);
});
