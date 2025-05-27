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
