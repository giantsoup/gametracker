<?php

namespace Tests\Feature\Livewire\Events;

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
            ->set('duration', 45)
            ->set('total_points', 12)
            ->set('total_placements', 3)
            ->set('points_distribution', [7, 3, 2])
            ->assertSet('name', 'Test Game')
            ->assertSet('duration', 45)
            ->assertSet('total_points', 12)
            ->assertSet('total_placements', 3)
            ->assertSet('points_distribution', [7, 3, 2]);
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
            ->set('duration', 45)
            ->set('total_points', 12)
            ->set('total_placements', 3)
            ->set('points_distribution', [7, 3, 2])
            ->set('selectedPlayerIds', [$player->id])
            ->call('createGame')
            ->assertSet('name', '')
            ->assertSet('duration', 60) // Default value
            ->assertSet('total_points', Game::DEFAULT_TOTAL_POINTS)
            ->assertSet('total_placements', Game::DEFAULT_TOTAL_PLACEMENTS)
            ->assertSet('points_distribution', Game::DEFAULT_POINTS_DISTRIBUTION)
            ->assertSet('selectedPlayerIds', [])
            ->assertSet('showForm', false);

        $this->assertDatabaseHas('games', [
            'name' => 'Test Game',
            'duration' => 45,
            'event_id' => $event->id,
            'total_points' => 12,
        ]);

        $game = Game::where('name', 'Test Game')->first();

        $this->assertSame([7, 3, 2], $game->points_distribution);

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
    public function create_game_form_regenerates_distribution_from_totals()
    {
        $event = Event::factory()->create();

        Livewire::test(CreateGameForm::class, ['event' => $event])
            ->set('total_points', 12)
            ->set('total_placements', 4)
            ->call('regeneratePointsDistribution')
            ->assertSet('points_distribution', [5, 4, 2, 1]);
    }

    #[Test]
    public function create_game_form_updates_total_points_when_placement_values_change()
    {
        $event = Event::factory()->create();

        Livewire::test(CreateGameForm::class, ['event' => $event])
            ->assertSet('total_points', Game::DEFAULT_TOTAL_POINTS)
            ->assertSet('points_distribution', Game::DEFAULT_POINTS_DISTRIBUTION)
            ->call('increasePlacementPoints', 2)
            ->assertSet('points_distribution', [5, 3, 2])
            ->assertSet('total_points', 10)
            ->call('decreasePlacementPoints', 0)
            ->assertSet('points_distribution', [4, 3, 2])
            ->assertSet('total_points', 9);
    }
}
