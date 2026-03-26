<?php

use App\Models\Event;
use App\Models\Game;
use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('game belongs to an event', function () {
    $event = Event::factory()->create();
    $game = Game::factory()->create(['event_id' => $event->id]);

    expect($game->event)->toBeInstanceOf(Event::class);
    expect($game->event->id)->toBe($event->id);
});

test('game can have many owners', function () {
    $game = Game::factory()->create();
    $players = Player::factory()->count(3)->create();

    $game->owners()->attach($players);

    expect($game->owners)->toHaveCount(3);
    expect($game->owners->first())->toBeInstanceOf(Player::class);
});

test('game uses soft deletes', function () {
    $game = Game::factory()->create();

    $game->delete();

    expect(Game::count())->toBe(0);
    expect(Game::withTrashed()->count())->toBe(1);
    expect(Game::withTrashed()->first()->deleted_at)->not->toBeNull();
});

test('getDurationForHumans formats duration correctly', function () {
    // Test hours and minutes
    $game = Game::factory()->create(['duration' => 90]);
    expect($game->getDurationForHumans())->toBe('1h 30m');

    // Test hours only
    $game = Game::factory()->create(['duration' => 60]);
    expect($game->getDurationForHumans())->toBe('1h');

    // Test minutes only
    $game = Game::factory()->create(['duration' => 45]);
    expect($game->getDurationForHumans())->toBe('45m');

    // Test multiple hours
    $game = Game::factory()->create(['duration' => 150]);
    expect($game->getDurationForHumans())->toBe('2h 30m');
});

test('game can be restored after soft delete', function () {
    $game = Game::factory()->create();

    $game->delete();
    expect(Game::count())->toBe(0);

    $game->restore();
    expect(Game::count())->toBe(1);
});

test('game can be force deleted', function () {
    $game = Game::factory()->create();

    $game->forceDelete();

    expect(Game::count())->toBe(0);
    expect(Game::withTrashed()->count())->toBe(0);
});

test('game resolves points by placement from its configured distribution', function () {
    $game = Game::factory()->create([
        'total_points' => 10,
        'points_distribution' => [6, 3, 1],
    ]);

    expect($game->pointsForPlacement(1))->toBe(6)
        ->and($game->pointsForPlacement(2))->toBe(3)
        ->and($game->pointsForPlacement(3))->toBe(1)
        ->and($game->pointsForPlacement(4))->toBe(0)
        ->and($game->pointsForPlacement(null))->toBe(0)
        ->and($game->formattedPointsDistribution())->toBe('6, 3, 1');
});

test('game validates points distribution input against the total points', function () {
    expect(Game::normalizePointsDistribution([6, '3', -1], 4))->toBe([6, 3, 0, 0])
        ->and(Game::pointsDistributionArrayValidationMessage([6, 3, 1], 10, 3))->toBeNull()
        ->and(Game::pointsDistributionArrayValidationMessage('6, 3, 1', 10, 3))->toBe('Points distribution must be a list of placement values.')
        ->and(Game::pointsDistributionArrayValidationMessage([6, 3], 9, 3))->toBe('Points distribution must include 3 placements.')
        ->and(Game::pointsDistributionArrayValidationMessage([6, 'three', 1], 10, 3))->toBe('Each placement value must be a whole number.')
        ->and(Game::pointsDistributionArrayValidationMessage([6, -3, 1], 4, 3))->toBe('Placement values cannot be negative.')
        ->and(Game::pointsDistributionArrayValidationMessage([6, 3, 1], 12, 3))->toBe('Points distribution must add up to 12.');
});

test('game can generate a balanced default distribution for any placement count', function () {
    expect(Game::defaultPointsDistribution())->toBe(Game::DEFAULT_POINTS_DISTRIBUTION)
        ->and(Game::defaultPointsDistribution(11, 5))->toBe([4, 3, 2, 1, 1])
        ->and(Game::defaultPointsDistribution(12, 4))->toBe([5, 4, 2, 1]);
});
