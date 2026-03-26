<?php

use App\Models\Event;
use App\Models\Game;
use App\Models\GamePoint;
use App\Models\Player;
use App\Models\User;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('demo data seeder creates realistic event rosters and live game states', function () {
    $this->seed(DemoDataSeeder::class);

    $events = Event::query()
        ->with([
            'players.user',
            'games.owners.user',
            'games.points.assignedBy',
        ])
        ->get()
        ->keyBy('name');

    expect($events)->toHaveCount(3)
        ->and(Event::active()->count())->toBe(1);

    $events->each(function (Event $event): void {
        expect($event->players->count())->toBeGreaterThanOrEqual(12)
            ->toBeLessThanOrEqual(15);
        expect($event->games)->toHaveCount(6);
        expect($event->games->every(
            fn (Game $game): bool => $game->owners->count() >= ($event->players->count() - 2)
        ))->toBeTrue();

        $playerAppearances = $event->games
            ->flatMap(fn (Game $game) => $game->owners->pluck('id'))
            ->countBy();

        $alwaysPlayingCount = $playerAppearances
            ->filter(fn (int $count): bool => $count === $event->games->count())
            ->count();

        expect($alwaysPlayingCount)->toBeGreaterThan(intdiv($event->players->count(), 2));
    });

    $liveEvent = $events->get('Current Game Night');

    expect($liveEvent)->toBeInstanceOf(Event::class);
    expect($liveEvent->active)->toBeTrue();
    expect($liveEvent->started_at)->not->toBeNull();
    expect($liveEvent->ended_at)->toBeNull();

    $playedGames = $liveEvent->games->filter(fn (Game $game): bool => $game->points->isNotEmpty());
    $currentGames = $liveEvent->games->filter(
        fn (Game $game): bool => $game->created_at !== null && $game->points->isEmpty()
    );
    $upcomingGames = $liveEvent->games->filter(fn (Game $game): bool => $game->created_at === null);

    expect($playedGames)->toHaveCount(3)
        ->and($currentGames)->toHaveCount(1)
        ->and($upcomingGames)->toHaveCount(2);

    expect($playedGames->every(
        fn (Game $game): bool => $game->points->count() === $game->owners->count()
    ))->toBeTrue();

    expect($playedGames->every(function (Game $game): bool {
        $placements = $game->points->pluck('placement')->sort()->values()->all();
        $expectedPlacements = range(1, $game->owners->count());

        return $placements === $expectedPlacements;
    }))->toBeTrue();

    expect($playedGames->every(function (Game $game): bool {
        return $game->points
            ->sortBy('placement')
            ->take(3)
            ->pluck('points')
            ->values()
            ->all() === [5, 3, 1];
    }))->toBeTrue();

    expect($playedGames->every(function (Game $game): bool {
        return $game->points->every(
            fn (GamePoint $gamePoint): bool => $gamePoint->assignedBy?->email === 'admin@example.com'
        );
    }))->toBeTrue();
});

test('demo data seeder can be run repeatedly without duplicating demo records', function () {
    $this->seed(DemoDataSeeder::class);
    $this->seed(DemoDataSeeder::class);

    expect(User::count())->toBe(17)
        ->and(Event::count())->toBe(3)
        ->and(Player::count())->toBe(42)
        ->and(Game::count())->toBe(18)
        ->and(GamePoint::count())->toBe(120)
        ->and(Event::active()->count())->toBe(1);
});
