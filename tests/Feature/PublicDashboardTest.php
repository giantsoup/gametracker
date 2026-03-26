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
        $event = Event::factory()->create([
            'name' => 'Test Active Event',
            'active' => true,
            'starts_at' => now()->subHour(),
            'started_at' => now()->subHour(),
        ]);

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
        $event = Event::factory()->create([
            'name' => 'Test Active Event',
            'active' => true,
            'starts_at' => now()->subHour(),
            'started_at' => now()->subHour(),
        ]);

        $currentGame = Game::factory()->create([
            'name' => 'Current Game',
            'event_id' => $event->id,
            'created_at' => now(),
        ]);

        $finishedGame = Game::factory()->create([
            'name' => 'Finished Game',
            'event_id' => $event->id,
            'created_at' => now()->subHour(),
        ]);

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
        $response->assertSee('Up Next');
    }
}
