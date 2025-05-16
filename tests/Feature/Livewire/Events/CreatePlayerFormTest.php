<?php

namespace Tests\Feature\Livewire\Events;

use App\Livewire\Events\CreatePlayerForm;
use App\Models\Event;
use App\Models\Player;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CreatePlayerFormTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function create_player_form_can_render()
    {
        $event = Event::factory()->create();

        Livewire::test(CreatePlayerForm::class, ['event' => $event])
            ->assertStatus(200);
    }

    /** @test */
    public function create_player_form_can_toggle_visibility()
    {
        $event = Event::factory()->create();

        Livewire::test(CreatePlayerForm::class, ['event' => $event])
            ->assertSet('showForm', false)
            ->call('toggleForm')
            ->assertSet('showForm', true)
            ->call('toggleForm')
            ->assertSet('showForm', false);
    }

    /** @test */
    public function create_player_form_displays_all_users()
    {
        $event = Event::factory()->create();
        $user1 = User::factory()->create(['name' => 'John Doe']);
        $user2 = User::factory()->create(['name' => 'Jane Smith']);

        Livewire::test(CreatePlayerForm::class, ['event' => $event])
            ->call('toggleForm') // Toggle form visibility to true
            ->assertSee('John Doe')
            ->assertSee('Jane Smith');
    }

    /** @test */
    public function create_player_form_can_set_user_id()
    {
        $event = Event::factory()->create();
        $user = User::factory()->create(['name' => 'John Doe']);

        Livewire::test(CreatePlayerForm::class, ['event' => $event])
            ->set('userId', $user->id)
            ->assertSet('userId', $user->id);
    }

    /** @test */
    public function create_player_form_can_create_player()
    {
        $event = Event::factory()->create();
        $user = User::factory()->create();

        Livewire::test(CreatePlayerForm::class, ['event' => $event])
            ->set('userId', $user->id)
            ->set('nickname', 'TestNickname')
            ->call('createPlayer')
            ->assertSet('userId', null)
            ->assertSet('nickname', null)
            ->assertSet('showForm', false);

        $this->assertDatabaseHas('players', [
            'user_id' => $user->id,
            'event_id' => $event->id,
            'nickname' => 'TestNickname',
        ]);
    }

    /** @test */
    public function create_player_form_validates_user_id()
    {
        $event = Event::factory()->create();

        Livewire::test(CreatePlayerForm::class, ['event' => $event])
            ->set('nickname', 'TestNickname')
            ->call('createPlayer')
            ->assertHasErrors(['userId' => 'required']);
    }

    /** @test */
    public function create_player_form_prevents_duplicate_players()
    {
        $event = Event::factory()->create();
        $user = User::factory()->create();

        // Create a player for this user and event
        Player::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);

        Livewire::test(CreatePlayerForm::class, ['event' => $event])
            ->set('userId', $user->id)
            ->set('nickname', 'TestNickname')
            ->call('createPlayer')
            ->assertHasErrors('userId');

        // Ensure no new player was created
        $this->assertDatabaseCount('players', 1);
    }
}
