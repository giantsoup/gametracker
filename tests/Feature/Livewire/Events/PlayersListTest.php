<?php

namespace Tests\Feature\Livewire\Events;

use App\Livewire\Events\PlayersList;
use App\Models\Event;
use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PlayersListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function players_list_can_render()
    {
        $event = Event::factory()->create();

        Livewire::test(PlayersList::class, ['event' => $event])
            ->assertStatus(200);
    }

    /** @test */
    public function players_list_displays_players()
    {
        $event = Event::factory()->create();
        $players = Player::factory()->count(3)->create([
            'event_id' => $event->id,
        ]);

        Livewire::test(PlayersList::class, ['event' => $event])
            ->assertSee($players[0]->user->name)
            ->assertSee($players[1]->user->name)
            ->assertSee($players[2]->user->name);
    }

    /** @test */
    public function players_list_can_remove_player()
    {
        $event = Event::factory()->create();
        $player = Player::factory()->create([
            'event_id' => $event->id,
        ]);

        Livewire::test(PlayersList::class, ['event' => $event])
            ->assertSee($player->user->name)
            ->call('removePlayer', $player)
            ->assertDontSee($player->user->name);

        $this->assertSoftDeleted('players', [
            'id' => $player->id,
        ]);
    }

    /** @test */
    public function players_list_can_mark_player_as_joined()
    {
        $event = Event::factory()->create();
        $player = Player::factory()->create([
            'event_id' => $event->id,
            'joined_at' => null,
        ]);

        Livewire::test(PlayersList::class, ['event' => $event])
            ->call('markPlayerJoined', $player);

        $this->assertDatabaseHas('players', [
            'id' => $player->id,
            'joined_at' => now()->toDateTimeString(),
        ]);
    }

    /** @test */
    public function players_list_can_mark_player_as_left()
    {
        $event = Event::factory()->create();
        $player = Player::factory()->joined()->create([
            'event_id' => $event->id,
            'left_at' => null,
        ]);

        Livewire::test(PlayersList::class, ['event' => $event])
            ->call('markPlayerLeft', $player);

        $this->assertDatabaseHas('players', [
            'id' => $player->id,
            'left_at' => now()->toDateTimeString(),
        ]);
    }
}
