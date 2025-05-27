<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Game;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_dashboard_can_be_rendered()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSeeLivewire('dashboard');
    }

    public function test_public_dashboard_shows_active_event()
    {
        // Create an active event
        $event = Event::factory()->create([
            'name' => 'Test Active Event',
            'active' => true,
            'starts_at' => now()->subHour(),
            'started_at' => now()->subHour(),
        ]);

        // Create a game for the event
        $game = Game::factory()->create([
            'name' => 'Test Game',
            'event_id' => $event->id,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Test Active Event');
        $response->assertSee('Test Game');
    }

    public function test_public_dashboard_shows_finished_and_upcoming_games()
    {
        // Create an active event
        $event = Event::factory()->create([
            'name' => 'Test Active Event',
            'active' => true,
            'starts_at' => now()->subHour(),
            'started_at' => now()->subHour(),
        ]);

        // Create a current game for the event
        $currentGame = Game::factory()->create([
            'name' => 'Current Game',
            'event_id' => $event->id,
            'created_at' => now(),
        ]);

        // Create a finished game for the event
        $finishedGame = Game::factory()->create([
            'name' => 'Finished Game',
            'event_id' => $event->id,
            'created_at' => now()->subHour(),
        ]);

        // Create an upcoming game for the event
        // Note: We're setting created_at to null to mark it as upcoming
        $upcomingGame = Game::factory()->create([
            'name' => 'Upcoming Game',
            'event_id' => $event->id,
        ]);
        $upcomingGame->created_at = null;
        $upcomingGame->save();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Current Game');
        $response->assertSee('Finished Game');
        $response->assertSee('Upcoming Game');
        $response->assertSee('Previously Played');
        $response->assertSee('Upcoming Games');
    }

    public function test_public_dashboard_can_switch_layouts()
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        // Test that we can switch to layout 2
        \Livewire\Livewire::test('dashboard')
            ->call('switchLayout', 2)
            ->assertSet('activeLayout', 2);

        // Test that we can switch to layout 3
        \Livewire\Livewire::test('dashboard')
            ->call('switchLayout', 3)
            ->assertSet('activeLayout', 3);

        // Test that we can switch back to layout 1
        \Livewire\Livewire::test('dashboard')
            ->call('switchLayout', 1)
            ->assertSet('activeLayout', 1);
    }
}
