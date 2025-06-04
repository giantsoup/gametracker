<?php

namespace Tests\Feature\Livewire\Events;

use App\Enums\GameStatus;
use App\Livewire\Events\CreateGameForm;
use App\Models\Event;
use App\Models\Game;
use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateGameFormTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function create_game_form_can_render()
    {
        $event = Event::factory()->create();

        Livewire::test(CreateGameForm::class, ['event' => $event])
            ->assertStatus(200);
    }

    #[Test]
    public function create_game_form_can_toggle_visibility()
    {
        $event = Event::factory()->create();

        Livewire::test(CreateGameForm::class, ['event' => $event])
            ->assertSet('showForm', false)
            ->call('toggleForm')
            ->assertSet('showForm', true)
            ->call('toggleForm')
            ->assertSet('showForm', false);
    }

    #[Test]
    public function create_game_form_displays_all_players()
    {
        $event = Event::factory()->create();
        $player1 = Player::factory()->create([
            'event_id' => $event->id,
            'nickname' => 'Player One',
        ]);
        $player2 = Player::factory()->create([
            'event_id' => $event->id,
            'nickname' => 'Player Two',
        ]);

        Livewire::test(CreateGameForm::class, ['event' => $event])
            ->call('toggleForm') // Toggle form visibility to true
            ->assertSee('Player One')
            ->assertSee('Player Two');
    }

    #[Test]
    public function create_game_form_can_set_values()
    {
        $event = Event::factory()->create();

        Livewire::test(CreateGameForm::class, ['event' => $event])
            ->set('name', 'Test Game')
            ->set('description', 'A test game description')
            ->set('rules', 'Test game rules')
            ->set('duration', 45)
            ->assertSet('name', 'Test Game')
            ->assertSet('description', 'A test game description')
            ->assertSet('rules', 'Test game rules')
            ->assertSet('duration', 45);
    }

    #[Test]
    public function create_game_form_can_create_game()
    {
        $event = Event::factory()->create();
        $player = Player::factory()->create([
            'event_id' => $event->id,
        ]);

        Livewire::test(CreateGameForm::class, ['event' => $event])
            ->set('name', 'Test Game')
            ->set('description', 'A test game description')
            ->set('rules', 'Test game rules')
            ->set('duration', 45)
            ->set('selectedPlayerIds', [$player->id])
            ->call('createGame')
            ->assertSet('name', '')
            ->assertSet('description', '')
            ->assertSet('rules', '')
            ->assertSet('duration', 60) // Default value
            ->assertSet('selectedPlayerIds', [])
            ->assertSet('showForm', false);

        $this->assertDatabaseHas('games', [
            'name' => 'Test Game',
            'description' => 'A test game description',
            'rules' => 'Test game rules',
            'duration' => 45,
            'event_id' => $event->id,
            'status' => GameStatus::Unplayed->value,
        ]);

        $game = Game::where('name', 'Test Game')->first();
        $this->assertDatabaseHas('game_player', [
            'game_id' => $game->id,
            'player_id' => $player->id,
        ]);
    }

    #[Test]
    public function create_game_form_validates_name()
    {
        $event = Event::factory()->create();

        Livewire::test(CreateGameForm::class, ['event' => $event])
            ->set('duration', 45)
            ->call('createGame')
            ->assertHasErrors(['name' => 'required']);
    }

    #[Test]
    public function create_game_form_validates_duration()
    {
        $event = Event::factory()->create();

        Livewire::test(CreateGameForm::class, ['event' => $event])
            ->set('name', 'Test Game')
            ->set('duration', 10) // Not a multiple of 15
            ->call('createGame')
            ->assertHasErrors(['duration' => 'multiple_of']);
    }

    #[Test]
    public function create_game_form_validates_duration_minimum()
    {
        $event = Event::factory()->create();

        Livewire::test(CreateGameForm::class, ['event' => $event])
            ->set('name', 'Test Game')
            ->set('duration', 5) // Less than minimum of 15
            ->call('createGame')
            ->assertHasErrors(['duration' => 'min']);
    }

    #[Test]
    public function create_game_form_adds_all_event_players_except_owners()
    {
        $event = Event::factory()->create();

        // Create owner player
        $ownerPlayer = Player::factory()->create([
            'event_id' => $event->id,
            'nickname' => 'Owner Player',
        ]);

        // Create regular players
        $player1 = Player::factory()->create([
            'event_id' => $event->id,
            'nickname' => 'Regular Player 1',
        ]);

        $player2 = Player::factory()->create([
            'event_id' => $event->id,
            'nickname' => 'Regular Player 2',
        ]);

        Livewire::test(CreateGameForm::class, ['event' => $event])
            ->set('name', 'Test Game')
            ->set('duration', 60)
            ->set('selectedPlayerIds', [$ownerPlayer->id])
            ->call('createGame');

        $game = Game::where('name', 'Test Game')->first();

        // Verify owner player is attached as owner
        $this->assertDatabaseHas('game_player', [
            'game_id' => $game->id,
            'player_id' => $ownerPlayer->id,
        ]);

        // Verify regular players are attached as players
        $this->assertDatabaseHas('game_player', [
            'game_id' => $game->id,
            'player_id' => $player1->id,
        ]);

        $this->assertDatabaseHas('game_player', [
            'game_id' => $game->id,
            'player_id' => $player2->id,
        ]);

        // Verify the correct number of players are attached to the game
        $this->assertEquals(3, $game->players()->count());

        // Verify only the owner player is an owner
        $this->assertEquals(1, $game->owners()->count());
        $this->assertTrue($game->owners->contains($ownerPlayer->id));
    }
}
