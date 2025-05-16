<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Player;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_view_events_index()
    {
        $user = User::factory()->create();
        $events = Event::factory()->count(3)->create();

        $response = $this->actingAs($user)
            ->get(route('events.index'));

        $response->assertStatus(200);
        $response->assertViewIs('events.index');
        $response->assertViewHas('events');

        foreach ($events as $event) {
            $response->assertSee($event->name);
        }
    }

    /** @test */
    public function user_can_view_event_details()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('events.show', $event));

        $response->assertStatus(200);
        $response->assertViewIs('events.show');
        $response->assertViewHas('event');
        $response->assertSee($event->name);

        // Check for player management components
        $response->assertSeeLivewire('events.players-list');
        $response->assertSeeLivewire('events.create-player-form');
    }

    /** @test */
    public function user_can_see_players_in_event()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create();

        // Create some players for this event
        $players = Player::factory()->count(3)->create([
            'event_id' => $event->id,
        ]);

        $response = $this->actingAs($user)
            ->get(route('events.show', $event));

        $response->assertStatus(200);

        // Check that player names are visible
        foreach ($players as $player) {
            $response->assertSee($player->user->name);
        }
    }

    /** @test */
    public function guest_cannot_view_events()
    {
        $event = Event::factory()->create();

        $this->get(route('events.index'))
            ->assertRedirect(route('login'));

        $this->get(route('events.show', $event))
            ->assertRedirect(route('login'));
    }
}
