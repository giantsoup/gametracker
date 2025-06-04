<?php

namespace Tests\Feature\Livewire;

use App\Livewire\LoggedInDashboard;
use App\Models\Event;
use App\Models\Game;
use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoggedInDashboardTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function logged_in_dashboard_can_render()
    {
        Livewire::test(LoggedInDashboard::class)
            ->assertStatus(200);
    }

    #[Test]
    public function can_mark_player_as_left_from_game()
    {
        // Create an event
        $event = Event::factory()->create();

        // Create a game in the event
        $game = Game::factory()->create([
            'event_id' => $event->id,
        ]);

        // Create a player in the event
        $player = Player::factory()->create([
            'event_id' => $event->id,
        ]);

        // Add the player to the game
        $game->players()->attach($player->id);

        // Test marking the player as left
        Livewire::test(LoggedInDashboard::class)
            ->call('markPlayerLeftGame', $game->id, $player->id);

        // Verify the player is marked as left
        $this->assertNotNull($game->players()->where('player_id', $player->id)->first()->pivot->left_at);
    }

    #[Test]
    public function can_mark_player_as_active_in_game()
    {
        // Create an event
        $event = Event::factory()->create();

        // Create a game in the event
        $game = Game::factory()->create([
            'event_id' => $event->id,
        ]);

        // Create a player in the event
        $player = Player::factory()->create([
            'event_id' => $event->id,
        ]);

        // Add the player to the game and mark as left
        $game->players()->attach($player->id, ['left_at' => now()]);

        // Test marking the player as active
        Livewire::test(LoggedInDashboard::class)
            ->call('markPlayerActiveInGame', $game->id, $player->id);

        // Verify the player is marked as active (not left)
        $this->assertNull($game->players()->where('player_id', $player->id)->first()->pivot->left_at);
    }

    #[Test]
    public function all_event_players_are_added_to_game_except_owners()
    {
        // Create an event
        $event = Event::factory()->create();

        // Create players in the event
        $owner = Player::factory()->create([
            'event_id' => $event->id,
        ]);

        $player1 = Player::factory()->create([
            'event_id' => $event->id,
        ]);

        $player2 = Player::factory()->create([
            'event_id' => $event->id,
        ]);

        // Create a game with one owner
        $game = Game::factory()->create([
            'event_id' => $event->id,
        ]);

        $game->owners()->attach($owner->id);

        // Add all non-owner players to the game
        $allEventPlayerIds = $event->players()->pluck('id')->toArray();
        $ownerIds = $game->owners()->pluck('id')->toArray();
        $nonOwnerPlayerIds = array_diff($allEventPlayerIds, $ownerIds);
        $game->players()->attach($nonOwnerPlayerIds);

        // Verify all players are in the game
        $this->assertEquals(3, $game->players()->count());

        // Verify only the owner is an owner
        $this->assertEquals(1, $game->owners()->count());
        $this->assertTrue($game->owners->contains($owner->id));

        // Verify non-owner players are not left
        $this->assertNull($game->players()->where('player_id', $player1->id)->first()->pivot->left_at);
        $this->assertNull($game->players()->where('player_id', $player2->id)->first()->pivot->left_at);
    }
}
