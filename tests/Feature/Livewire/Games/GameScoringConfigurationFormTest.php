<?php

use App\Models\Event;
use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

test('create game form stores total points and points distribution', function () {
    $this->actingAs(User::factory()->create());

    $event = Event::factory()->create(['active' => true]);

    Volt::test('games.create-game-form')
        ->set('name', 'Points Configured Game')
        ->set('event_id', $event->id)
        ->set('duration', 45)
        ->set('total_points', 12)
        ->set('total_placements', 3)
        ->set('points_distribution', [7, 3, 2])
        ->call('create')
        ->assertDispatched('game-created');

    $this->assertDatabaseHas('games', [
        'name' => 'Points Configured Game',
        'event_id' => $event->id,
        'duration' => 45,
        'total_points' => 12,
    ]);

    expect(Game::query()->firstOrFail()->points_distribution)->toBe([7, 3, 2]);
});

test('create game form regenerates placement values from total points and total placements', function () {
    $this->actingAs(User::factory()->create());

    $event = Event::factory()->create(['active' => true]);

    Volt::test('games.create-game-form')
        ->set('total_points', 12)
        ->set('total_placements', 4)
        ->call('regeneratePointsDistribution')
        ->assertSet('points_distribution', [5, 4, 2, 1]);
});

test('create game form updates total points when placement values are adjusted', function () {
    $this->actingAs(User::factory()->create());

    Volt::test('games.create-game-form')
        ->assertSet('total_points', Game::DEFAULT_TOTAL_POINTS)
        ->assertSet('points_distribution', Game::DEFAULT_POINTS_DISTRIBUTION)
        ->call('increasePlacementPoints', 2)
        ->assertSet('points_distribution', [5, 3, 2])
        ->assertSet('total_points', 10)
        ->call('decreasePlacementPoints', 0)
        ->assertSet('points_distribution', [4, 3, 2])
        ->assertSet('total_points', 9);
});

test('create game form requires duration to stay in 15 minute intervals', function () {
    $this->actingAs(User::factory()->create());

    $event = Event::factory()->create(['active' => true]);

    Volt::test('games.create-game-form')
        ->set('name', 'Bad Duration Game')
        ->set('event_id', $event->id)
        ->set('duration', 50)
        ->set('total_points', 12)
        ->set('total_placements', 3)
        ->set('points_distribution', [7, 3, 2])
        ->call('create')
        ->assertHasErrors(['duration' => 'multiple_of']);
});

test('edit game form updates scoring configuration', function () {
    $this->actingAs(User::factory()->create());

    $game = Game::factory()->create([
        'total_points' => 9,
        'points_distribution' => [5, 3, 1],
    ]);

    Volt::test('games.edit-game-form', ['game' => $game])
        ->set('total_points', 15)
        ->set('total_placements', 4)
        ->set('points_distribution', [8, 4, 2, 1])
        ->call('update')
        ->assertRedirect(route('games.show', $game));

    expect($game->refresh()->total_points)->toBe(15)
        ->and($game->points_distribution)->toBe([8, 4, 2, 1]);
});
