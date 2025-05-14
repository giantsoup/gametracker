<?php

namespace Tests\Feature\Livewire\Admin\Events;

use App\Enums\UserRole;
use App\Livewire\Admin\Events\EventsTable;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EventsTableTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function events_table_can_render()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        // Act as the admin user
        $this->actingAs($admin);

        // Test that the component renders
        Livewire::test(EventsTable::class)
            ->assertStatus(200);
    }

    #[Test]
    public function events_table_displays_events()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        // Create some events
        $event1 = Event::create([
            'name' => 'Test Event 1',
            'active' => true,
            'starts_at' => now()->addDay(),
            'ends_at' => now()->addDays(2),
        ]);

        $event2 = Event::create([
            'name' => 'Test Event 2',
            'active' => false,
            'starts_at' => now()->addDays(3),
            'ends_at' => now()->addDays(4),
        ]);

        // Act as the admin user
        $this->actingAs($admin);

        // Test that the component displays the events
        Livewire::test(EventsTable::class)
            ->assertSee('Test Event 1')
            ->assertSee('Test Event 2');
    }

    #[Test]
    public function events_table_can_search_events()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        // Create some events
        $event1 = Event::create([
            'name' => 'Conference',
            'active' => true,
            'starts_at' => now()->addDay(),
            'ends_at' => now()->addDays(2),
        ]);

        $event2 = Event::create([
            'name' => 'Workshop',
            'active' => false,
            'starts_at' => now()->addDays(3),
            'ends_at' => now()->addDays(4),
        ]);

        // Act as the admin user
        $this->actingAs($admin);

        // Test that the search functionality works
        Livewire::test(EventsTable::class)
            ->set('search', 'Conference')
            ->assertSee('Conference')
            ->assertDontSee('Workshop');
    }

    #[Test]
    public function events_table_can_sort_events()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        // Create some events with different creation dates
        $event1 = Event::create([
            'name' => 'Conference',
            'active' => true,
            'starts_at' => now()->addDay(),
            'ends_at' => now()->addDays(2),
            'created_at' => now()->subDays(2),
        ]);

        $event2 = Event::create([
            'name' => 'Workshop',
            'active' => false,
            'starts_at' => now()->addDays(3),
            'ends_at' => now()->addDays(4),
            'created_at' => now()->subDay(),
        ]);

        // Act as the admin user
        $this->actingAs($admin);

        // Test that sorting by name works
        Livewire::test(EventsTable::class)
            ->call('sortBy', 'name')
            ->assertSet('sortField', 'name')
            ->assertSet('sortDirection', 'asc');
    }

    #[Test]
    public function admin_can_delete_event_from_table()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        // Create an event to delete
        $event = Event::create([
            'name' => 'Delete Me',
            'active' => true,
            'starts_at' => now()->addDay(),
            'ends_at' => now()->addDays(2),
        ]);

        // Act as the admin user
        $this->actingAs($admin);

        // Test that the delete functionality works
        Livewire::test(EventsTable::class)
            ->call('deleteModel', $event->id)
            ->assertDispatched('success');

        // Assert the event was soft deleted
        $this->assertSoftDeleted('events', [
            'id' => $event->id,
        ]);
    }

    #[Test]
    public function events_table_renders_active_status_badges_correctly()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        // Create active and inactive events
        $activeEvent = Event::create([
            'name' => 'Active Event',
            'active' => true,
            'starts_at' => now()->addDay(),
            'ends_at' => now()->addDays(2),
        ]);

        $inactiveEvent = Event::create([
            'name' => 'Inactive Event',
            'active' => false,
            'starts_at' => now()->addDays(3),
            'ends_at' => now()->addDays(4),
        ]);

        // Act as the admin user
        $this->actingAs($admin);

        // Get the component instance
        $component = new EventsTable;

        // Test active badge rendering
        $activeBadge = $component->renderCustomColumn('active', $activeEvent);
        $this->assertNotNull($activeBadge);
        $this->assertStringContainsString('Active', (string) $activeBadge->render());
        $this->assertStringContainsString('green', (string) $activeBadge->render());

        // Test inactive badge rendering
        $inactiveBadge = $component->renderCustomColumn('active', $inactiveEvent);
        $this->assertNotNull($inactiveBadge);
        $this->assertStringContainsString('Inactive', (string) $inactiveBadge->render());
        $this->assertStringContainsString('zinc', (string) $inactiveBadge->render());
    }
}
