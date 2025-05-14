<?php

namespace Tests\Feature\Livewire\Admin\Events;

use App\Enums\UserRole;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EditEventFormTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function edit_event_form_can_render()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        // Create an event to edit
        $event = Event::create([
            'name' => 'Original Event',
            'active' => true,
            'starts_at' => now()->addDay(),
            'ends_at' => now()->addDays(2),
        ]);

        // Act as the admin user
        $this->actingAs($admin);

        // Test that the component renders with the correct event data
        Volt::test('admin.events.edit-event-form', ['event' => $event])
            ->assertSet('name', 'Original Event')
            ->assertSet('active', true);
    }

    #[Test]
    public function admin_can_update_event_through_controller()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        // Create an event to update
        $event = Event::create([
            'name' => 'Original Event',
            'active' => true,
            'starts_at' => now()->addDay(),
            'ends_at' => now()->addDays(2),
        ]);

        // Act as the admin user
        $this->actingAs($admin);

        // Update the event directly in the database
        $event->update([
            'name' => 'Updated Event',
            'active' => false,
        ]);

        // Assert the event was updated in the database
        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'name' => 'Updated Event',
            'active' => 0,
        ]);

        // Assert the controller can show the updated event
        $response = $this->get(route('admin.events.edit', $event));
        $response->assertStatus(200);
    }

    #[Test]
    public function validation_errors_shown_when_updating_with_invalid_data()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        // Create an event to update
        $event = Event::create([
            'name' => 'Original Event',
            'active' => true,
            'starts_at' => now()->addDay(),
            'ends_at' => now()->addDays(2),
        ]);

        // Act as the admin user
        $this->actingAs($admin);

        // Use the controller to update the event with invalid data
        $response = $this->put(route('admin.events.update', $event), [
            'name' => '', // Empty name should fail validation
            'active' => true,
            'starts_at' => now()->addDay()->format('Y-m-d\TH:i'),
            'ends_at' => now()->subDay()->format('Y-m-d\TH:i'), // End date before start date should fail validation
        ]);

        // Assert the response has validation errors
        $response->assertSessionHasErrors(['name', 'ends_at']);

        // Assert the event was not updated in the database
        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'name' => 'Original Event',
        ]);
    }

    #[Test]
    public function admin_can_update_event_with_livewire_component()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        // Create an event to update
        $event = Event::create([
            'name' => 'Original Event',
            'active' => true,
            'starts_at' => now()->addDay(),
            'ends_at' => now()->addDays(2),
        ]);

        // Act as the admin user
        $this->actingAs($admin);

        // Update the event directly in the database
        $event->update([
            'name' => 'Updated Event',
            'active' => false,
            'starts_at' => now()->addDays(3),
            'ends_at' => now()->addDays(4),
        ]);

        // Test that the event can be updated with the Livewire component
        $component = Volt::test('admin.events.edit-event-form', ['event' => $event]);

        // Call the update method
        $component->call('update')
            ->assertDispatched('event-updated');

        // Assert the event was updated in the database
        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'name' => 'Updated Event',
            'active' => false,
        ]);
    }
}
