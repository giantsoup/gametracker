<?php

namespace Tests\Feature\Livewire\Events;

use App\Livewire\Events\GamesList;
use App\Models\Event;
use App\Models\Game;
use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GamesListTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function games_list_can_render()
    {
        $event = Event::factory()->create();

        Livewire::test(GamesList::class, ['event' => $event])
            ->assertStatus(200);
    }

    #[Test]
    public function games_list_displays_games()
    {
        $event = Event::factory()->create();
        $games = Game::factory()->count(3)->create([
            'event_id' => $event->id,
        ]);

        Livewire::test(GamesList::class, ['event' => $event])
            ->assertSee($games[0]->name)
            ->assertSee($games[1]->name)
            ->assertSee($games[2]->name);
    }

    #[Test]
    public function games_list_displays_game_duration()
    {
        $event = Event::factory()->create();
        $game = Game::factory()->create([
            'event_id' => $event->id,
            'duration' => 60, // 1 hour
        ]);

        Livewire::test(GamesList::class, ['event' => $event])
            ->assertSee('1h');
    }

    #[Test]
    public function games_list_displays_game_owners()
    {
        $event = Event::factory()->create();
        $game = Game::factory()->create([
            'event_id' => $event->id,
        ]);

        $player = Player::factory()->create([
            'event_id' => $event->id,
        ]);

        $game->owners()->attach($player->id);

        Livewire::test(GamesList::class, ['event' => $event])
            ->assertSee($player->getDisplayName());
    }

    #[Test]
    public function games_list_can_remove_game()
    {
        $event = Event::factory()->create();
        $game = Game::factory()->create([
            'event_id' => $event->id,
        ]);

        Livewire::test(GamesList::class, ['event' => $event])
            ->assertSee($game->name)
            ->call('removeGame', $game)
            ->assertDontSee($game->name);

        $this->assertSoftDeleted('games', [
            'id' => $game->id,
        ]);
    }
}
