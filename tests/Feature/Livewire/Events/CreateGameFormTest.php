<?php

namespace Tests\Feature\Livewire\Events;

use App\Livewire\Events\CreateGameForm;
use App\Models\Event;
use App\Models\Game;
use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CreateGameFormTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function create_game_form_can_render()
    {
        $event = Event::factory()->create();

        Livewire::test(CreateGameForm::class, ['event' => $event])
            ->assertStatus(200);
    }

    /** @test */
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

    /** @test */
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

    /** @test */
    public function create_game_form_can_set_values()
    {
        $event = Event::factory()->create();

        Livewire::test(CreateGameForm::class, ['event' => $event])
            ->set('name', 'Test Game')
            ->set('duration', 45)
            ->assertSet('name', 'Test Game')
            ->assertSet('duration', 45);
    }

    /** @test */
    public function create_game_form_can_create_game()
    {
        $event = Event::factory()->create();
        $player = Player::factory()->create([
            'event_id' => $event->id,
        ]);

        Livewire::test(CreateGameForm::class, ['event' => $event])
            ->set('name', 'Test Game')
            ->set('duration', 45)
            ->set('selectedPlayerIds', [$player->id])
            ->call('createGame')
            ->assertSet('name', '')
            ->assertSet('duration', 60) // Default value
            ->assertSet('selectedPlayerIds', [])
            ->assertSet('showForm', false);

        $this->assertDatabaseHas('games', [
            'name' => 'Test Game',
            'duration' => 45,
            'event_id' => $event->id,
        ]);

        $game = Game::where('name', 'Test Game')->first();
        $this->assertDatabaseHas('game_player', [
            'game_id' => $game->id,
            'player_id' => $player->id,
        ]);
    }

    /** @test */
    public function create_game_form_validates_name()
    {
        $event = Event::factory()->create();

        Livewire::test(CreateGameForm::class, ['event' => $event])
            ->set('duration', 45)
            ->call('createGame')
            ->assertHasErrors(['name' => 'required']);
    }

    /** @test */
    public function create_game_form_validates_duration()
    {
        $event = Event::factory()->create();

        Livewire::test(CreateGameForm::class, ['event' => $event])
            ->set('name', 'Test Game')
            ->set('duration', 10) // Not a multiple of 15
            ->call('createGame')
            ->assertHasErrors(['duration' => 'multiple_of']);
    }

    /** @test */
    public function create_game_form_validates_duration_minimum()
    {
        $event = Event::factory()->create();

        Livewire::test(CreateGameForm::class, ['event' => $event])
            ->set('name', 'Test Game')
            ->set('duration', 5) // Less than minimum of 15
            ->call('createGame')
            ->assertHasErrors(['duration' => 'min']);
    }
}
