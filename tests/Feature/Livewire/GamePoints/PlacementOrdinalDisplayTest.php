<?php

use App\Livewire\GamePoints\DisplayGamePoints;
use App\Livewire\GamePoints\PlayerTotalPoints;
use App\Models\Event;
use App\Models\Game;
use App\Models\GamePoint;
use App\Models\Player;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('display game points shows ordinal placement suffixes', function () {
    $event = Event::factory()->create();
    $game = Game::factory()->create([
        'event_id' => $event->id,
    ]);
    $assignedBy = User::factory()->create();

    $users = User::factory()->count(14)->create();

    foreach ($users as $index => $user) {
        $player = Player::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);

        $game->owners()->attach($player->id);

        GamePoint::create([
            'game_id' => $game->id,
            'player_id' => $user->id,
            'points' => max(0, 5 - $index),
            'placement' => $index + 1,
            'assigned_by' => $assignedBy->id,
            'assigned_at' => now(),
        ]);
    }

    Livewire::test(DisplayGamePoints::class, ['game' => $game])
        ->assertSee('1st')
        ->assertSee('2nd')
        ->assertSee('3rd')
        ->assertSee('4th')
        ->assertSee('11th')
        ->assertSee('12th')
        ->assertSee('13th')
        ->assertSee('14th')
        ->assertSee('bg-amber-100', false)
        ->assertSee('bg-slate-100', false)
        ->assertSee('bg-orange-100', false);
});

test('player total breakdown shows ordinal placement suffixes', function () {
    $user = User::factory()->create();
    $assignedBy = User::factory()->create();
    $event = Event::factory()->create();

    foreach ([2, 3, 11, 12, 13, 14] as $placement) {
        $game = Game::factory()->create([
            'event_id' => $event->id,
        ]);

        GamePoint::create([
            'game_id' => $game->id,
            'player_id' => $user->id,
            'points' => 0,
            'placement' => $placement,
            'assigned_by' => $assignedBy->id,
            'assigned_at' => now(),
        ]);
    }

    Livewire::test(PlayerTotalPoints::class, ['user' => $user])
        ->call('toggleBreakdown')
        ->assertSee('1st Place')
        ->assertSee('2nd')
        ->assertSee('3rd')
        ->assertSee('11th')
        ->assertSee('12th')
        ->assertSee('13th')
        ->assertSee('14th')
        ->assertSee('bg-amber-100', false)
        ->assertSee('bg-slate-100', false)
        ->assertSee('bg-orange-100', false);
});
