<?php

use App\Livewire\GamePoints\AssignPoints;
use App\Livewire\GamePoints\ModifyPoints;
use App\Models\Event;
use App\Models\Game;
use App\Models\GamePoint;
use App\Models\Player;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('assign points uses the game scoring configuration for each placement', function () {
    $owner = User::factory()->create();
    $event = Event::factory()->create();
    $game = Game::factory()->create([
        'event_id' => $event->id,
        'total_points' => 12,
        'points_distribution' => [7, 3, 2],
    ]);

    $players = User::factory()->count(3)->create();

    foreach ($players as $playerUser) {
        $player = Player::factory()->create([
            'event_id' => $event->id,
            'user_id' => $playerUser->id,
        ]);

        $game->owners()->attach($player->id);
    }

    $this->actingAs($owner);

    Livewire::test(AssignPoints::class, ['game' => $game])
        ->set('selectedPlayers.1', $players[0]->id)
        ->set('selectedPlayers.2', $players[1]->id)
        ->set('selectedPlayers.3', $players[2]->id)
        ->call('savePoints')
        ->assertDispatched('points-saved')
        ->assertDispatched('modal-close', name: 'assign-points-modal');

    $this->assertDatabaseHas('game_points', [
        'game_id' => $game->id,
        'player_id' => $players[0]->id,
        'placement' => 1,
        'points' => 7,
        'assigned_by' => $owner->id,
    ]);

    $this->assertDatabaseHas('game_points', [
        'game_id' => $game->id,
        'player_id' => $players[1]->id,
        'placement' => 2,
        'points' => 3,
        'assigned_by' => $owner->id,
    ]);

    $this->assertDatabaseHas('game_points', [
        'game_id' => $game->id,
        'player_id' => $players[2]->id,
        'placement' => 3,
        'points' => 2,
        'assigned_by' => $owner->id,
    ]);
});

test('modify points recalculates stored points from the updated placement', function () {
    $owner = User::factory()->create();
    $player = User::factory()->create();
    $event = Event::factory()->create();
    $game = Game::factory()->create([
        'event_id' => $event->id,
        'total_points' => 12,
        'points_distribution' => [7, 3, 2],
    ]);

    GamePoint::create([
        'game_id' => $game->id,
        'player_id' => $player->id,
        'points' => 7,
        'placement' => 1,
        'assigned_by' => $owner->id,
        'assigned_at' => now(),
    ]);

    $gamePoint = GamePoint::query()->firstOrFail();

    $this->actingAs($owner);

    Livewire::test(ModifyPoints::class, ['gamePoint' => $gamePoint->id])
        ->set('placement', 3)
        ->call('calculatePointsFromPlacement')
        ->assertSet('points', 2)
        ->call('updatePoints')
        ->assertDispatched('points-updated');

    $this->assertDatabaseHas('game_points', [
        'id' => $gamePoint->id,
        'placement' => 3,
        'points' => 2,
        'last_modified_by' => $owner->id,
    ]);
});

test('assign points rejects assigning the same player to multiple placements', function () {
    $owner = User::factory()->create();
    $event = Event::factory()->create();
    $game = Game::factory()->create([
        'event_id' => $event->id,
    ]);

    $players = User::factory()->count(2)->create();

    foreach ($players as $playerUser) {
        $player = Player::factory()->create([
            'event_id' => $event->id,
            'user_id' => $playerUser->id,
        ]);

        $game->owners()->attach($player->id);
    }

    $this->actingAs($owner);

    Livewire::test(AssignPoints::class, ['game' => $game])
        ->set('selectedPlayers.1', $players[0]->id)
        ->set('selectedPlayers.2', $players[0]->id)
        ->call('savePoints')
        ->assertHasErrors([
            'selectedPlayers.1',
            'selectedPlayers.2',
        ]);

    expect(GamePoint::query()->count())->toBe(0);
});

test('assign points only renders scoring placements and removes stale higher placements on save', function () {
    $owner = User::factory()->create();
    $event = Event::factory()->create();
    $game = Game::factory()->create([
        'event_id' => $event->id,
        'total_points' => 9,
        'points_distribution' => [5, 3, 1],
    ]);

    $players = User::factory()->count(4)->create();

    foreach ($players as $playerUser) {
        $player = Player::factory()->create([
            'event_id' => $event->id,
            'user_id' => $playerUser->id,
        ]);

        $game->owners()->attach($player->id);
    }

    GamePoint::create([
        'game_id' => $game->id,
        'player_id' => $players[3]->id,
        'points' => 0,
        'placement' => 4,
        'assigned_by' => $owner->id,
        'assigned_at' => now(),
    ]);

    $this->actingAs($owner);

    Livewire::test(AssignPoints::class, ['game' => $game])
        ->assertSee('1st')
        ->assertSee('2nd')
        ->assertSee('3rd')
        ->assertDontSee('4th')
        ->assertSee('bg-amber-100', false)
        ->assertSee('bg-slate-100', false)
        ->assertSee('bg-orange-100', false)
        ->set('selectedPlayers.1', $players[0]->id)
        ->set('selectedPlayers.2', $players[1]->id)
        ->set('selectedPlayers.3', $players[2]->id)
        ->call('savePoints')
        ->assertDispatched('points-saved')
        ->assertDispatched('modal-close', name: 'assign-points-modal');

    $this->assertDatabaseMissing('game_points', [
        'game_id' => $game->id,
        'placement' => 4,
    ]);
});

test('assign points reset clears the current placement selections without saving', function () {
    $owner = User::factory()->create();
    $event = Event::factory()->create();
    $game = Game::factory()->create([
        'event_id' => $event->id,
        'points_distribution' => [5, 3, 1],
    ]);

    $players = User::factory()->count(3)->create();

    foreach ($players as $playerUser) {
        $player = Player::factory()->create([
            'event_id' => $event->id,
            'user_id' => $playerUser->id,
        ]);

        $game->owners()->attach($player->id);
    }

    $this->actingAs($owner);

    Livewire::test(AssignPoints::class, ['game' => $game])
        ->assertSee('Reset')
        ->assertSee('Close')
        ->set('selectedPlayers.1', $players[0]->id)
        ->set('selectedPlayers.2', $players[1]->id)
        ->set('selectedPlayers.3', $players[2]->id)
        ->call('resetSelections')
        ->assertSet('selectedPlayers.1', null)
        ->assertSet('selectedPlayers.2', null)
        ->assertSet('selectedPlayers.3', null);

    expect(GamePoint::query()->count())->toBe(0);
});

test('modify points deletes the record when placement is cleared', function () {
    $owner = User::factory()->create();
    $player = User::factory()->create();
    $event = Event::factory()->create();
    $game = Game::factory()->create([
        'event_id' => $event->id,
    ]);

    $gamePoint = GamePoint::create([
        'game_id' => $game->id,
        'player_id' => $player->id,
        'points' => 5,
        'placement' => 1,
        'assigned_by' => $owner->id,
        'assigned_at' => now(),
    ]);

    $this->actingAs($owner);

    Livewire::test(ModifyPoints::class, ['gamePoint' => $gamePoint->id])
        ->set('placement', null)
        ->call('updatePoints')
        ->assertDispatched('points-updated');

    $this->assertDatabaseMissing('game_points', [
        'id' => $gamePoint->id,
    ]);
});

test('modify points rejects a placement already assigned to another player', function () {
    $owner = User::factory()->create();
    $event = Event::factory()->create();
    $game = Game::factory()->create([
        'event_id' => $event->id,
    ]);

    $firstPlayer = User::factory()->create();
    $secondPlayer = User::factory()->create();

    GamePoint::create([
        'game_id' => $game->id,
        'player_id' => $firstPlayer->id,
        'points' => 5,
        'placement' => 1,
        'assigned_by' => $owner->id,
        'assigned_at' => now(),
    ]);

    $gamePoint = GamePoint::create([
        'game_id' => $game->id,
        'player_id' => $secondPlayer->id,
        'points' => 3,
        'placement' => 2,
        'assigned_by' => $owner->id,
        'assigned_at' => now(),
    ]);

    $this->actingAs($owner);

    Livewire::test(ModifyPoints::class, ['gamePoint' => $gamePoint->id])
        ->set('placement', 1)
        ->call('updatePoints')
        ->assertHasErrors(['placement']);

    $this->assertDatabaseHas('game_points', [
        'id' => $gamePoint->id,
        'placement' => 2,
        'points' => 3,
    ]);
});
